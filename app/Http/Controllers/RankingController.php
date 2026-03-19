<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Models\Department;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * แสดงหน้ารายการตัวชี้วัดทั้งหมด (หน้า Index / ตารางข้อมูล)
     * มีระบบค้นหาและแบ่งหน้า (Pagination)
     */
    public function index(Request $request)
    {
        // เริ่มสร้าง Query Builder จาก Model Ranking
        $query = Ranking::query();

        // 1. ตรวจสอบว่ามีการส่งคำค้นหา (search) มาจากฟอร์มหรือไม่
        if ($search = $request->input('search')) {
            // ค้นหาจากชื่อตัวชี้วัด (ranking_name) หรือ รหัสตัวชี้วัด (ranking_code)
            $query->where('ranking_name', 'like', "%{$search}%")
                ->orWhere('ranking_code', 'like', "%{$search}%");
        }

        // 2. เก็บ URL ปัจจุบันไว้ใน Session
        // ประโยชน์: เพื่อให้เวลาแก้ไขหรือลบข้อมูลเสร็จ สามารถ Redirect กลับมาที่หน้าเดิม (พร้อมเงื่อนไขค้นหา/เลขหน้า) ได้
        session(['user_url' => request()->fullUrl()]);

        // 3. ดึงข้อมูลทั้งหมดที่ผ่านเงื่อนไขค้นหา แล้วนำมาเรียงลำดับรหัสตัวชี้วัด (sorting แบบตัวอักษรผสมตัวเลขให้ถูกต้องด้วย SORT_NATURAL)
        $allRankings = $query->get()->sortBy('ranking_code', SORT_NATURAL)->values();

        // 4. การแบ่งหน้า (Pagination) แบบ Manual เนื่องจากใช้ get() และ collection ไปแล้ว
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1; // หาหน้าปัจจุบัน
        $perPage = 50; // กำหนด 50 รายการต่อหน้า
        
        // แบ่งชุดข้อมูลทั้งหมด (Collection) ตามหน้าที่ต้องการ
        $rankings = new \Illuminate\Pagination\LengthAwarePaginator(
            $allRankings->forPage($page, $perPage)->values(),
            $allRankings->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()] // ระบุ path ปัจจุบันเพื่อให้หน้าถัดไปทำงานถูกต้อง
        );
        $rankings->appends(['search' => $search]); // แนบคำค้นหาต่อท้าย URL ในทุกลิงก์ของ pagination

        // ส่งข้อมูลตัวชี้วัด ($rankings) ไปแสดงผลที่ view rankings/index.blade.php
        return view('rankings.index', compact('rankings'));
    }

    /**
     * แสดงฟอร์มสําหรับหน้าเพิ่มข้อมูลตัวชี้วัดใหม่ (Create Form)
     */
    public function create()
    {
        // ดึงข้อมูลแผนก (Department) ทั้งหมด เพื่อนำไปแสดงใน Dropdown เลือกหน่วยงานที่รับผิดชอบ
        $departments = Department::all();
        return view('rankings.create', compact('departments'));
    }

    /**
     * รับข้อมูลจากฟอร์ม Create แล้วบันทึกลงฐานข้อมูล (Store)
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Validation) ตามกฎที่ตั้งไว้
        $request->validate([
            'ranking_code' => 'required', // รหัสตัวชี้วัด ห้ามว่าง
            'ranking_name' => 'required', // ชื่อตัวชี้วัด ห้ามว่าง
            'department_id' => 'required', // รหัสแผนก ห้ามว่าง
            'table_name' => 'required',   // ชื่อตารางฐานข้อมูลที่เก็บผลงาน ห้ามว่าง
            'hdc_link' => 'nullable|url', // อนุญาตให้ว่างได้ แต่ถ้ามีต้องเป็นรูปแบบ URL
            'target_value' => 'nullable|numeric', // ค่าเป้าหมาย อนุญาตให้ว่างได้ แต่ถ้ามีต้องเป็นตัวเลข
            'weight' => 'nullable|numeric',       // น้ำหนักคะแนน
            'score_0' => 'nullable|numeric',      // เกณฑ์คะแนน 0-5 ต้องเป็นตัวเลข
            'score_1_operator' => 'nullable|in:<,>=',
            'score_1' => 'nullable|numeric',      // เกณฑ์คะแนน 1-5 ต้องเป็นตัวเลข
            'score_2' => 'nullable|numeric',
            'score_2_5' => 'nullable|numeric',
            'score_3' => 'nullable|numeric',
            'score_4' => 'nullable|numeric',
            'score_5' => 'nullable|numeric',
        ]);

        $data = $request->all();
        
        // กำหนดค่าตั้งต้นให้ช้อมูล rank(คะแนนประเมิน) และ score_total(คะแนนรวมถ่วงน้ำหนัก) เป็น 0 ไว้ก่อน
        $data['rank'] = $data['rank'] ?? 0;
        $data['score_total'] = $data['score_total'] ?? 0;

        // 2. จัดการข้อมูลก่อนบันทึก
        // 2.1 กลุ่มที่อนุญาตให้ว่างเป็น null ได้ (เกณฑ์คะแนน)
        $scoreFields = ['score_0', 'score_1', 'score_2', 'score_2_5', 'score_3', 'score_4', 'score_5'];
        foreach ($scoreFields as $field) {
            $data[$field] = (isset($data[$field]) && $data[$field] !== '') ? $data[$field] : null;
        }

        // 2.2 กลุ่มที่ห้ามเป็น null ให้ตั้งค่าเป็น 0 (เป้าหมาย, น้ำหนัก)
        $requiredNumericFields = ['target_value', 'weight'];
        foreach ($requiredNumericFields as $field) {
            $data[$field] = (isset($data[$field]) && $data[$field] !== '') ? $data[$field] : 0;
        }

        // 3. บันทึกข้อมูลลงฐานข้อมูลโดยพาสข้อมูล Array (Mass Assignment)
        Ranking::create($data);
        
        // เมื่อบันทึกเสร็จ ให้เด้งกลับไปยังหน้า URL ก่อนหน้า (ที่เซฟไว้ใน Session 'user_url' ตอนเข้าหน้า Index)
        return redirect()->to(session('user_url', route('rankings.index')))->with('success', 'เพิ่มข้อมูลตัวชี้วัดเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียดของตัวชี้วัดแบบเจาะจงรายตัว (Show)
     */
    public function show(Ranking $ranking)
    {
        // ส่งตัวแปร Object $ranking ตัวที่ถูกเรียกดูไปแสดงผลในหน้า Show
        return view('rankings.show', compact('ranking'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลตัวชี้วัด (Edit Form)
     */
    public function edit(Ranking $ranking)
    {
        // ดึงรายชื่อแผนกเพื่อเอาไปทำ Dropdown
        $departments = Department::all();
        
        // ส่งข้อมูลตัวชี้วัดที่จะแก้ไข ($ranking) และรายการแผนกทั้งหมด ไปแสดงที่ฟอร์ม Edit
        return view('rankings.edit', compact('ranking', 'departments'));
    }

    /**
     * รับข้อมูลที่แก้ไขจากฟอร์มตรวจสอบความถูกต้องและบันทึกการเปลี่ยนแปลง (Update)
     */
    public function update(Request $request, Ranking $ranking)
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูล (เหมือนกับการ Store)
        $request->validate([
            'ranking_code' => 'required',
            'ranking_name' => 'required',
            'department_id' => 'required',
            'table_name' => 'required',
            'hdc_link' => 'nullable|url',
            'target_value' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'score_0' => 'nullable|numeric',
            'score_1_operator' => 'nullable|in:<,>=',
            'score_1' => 'nullable|numeric',
            'score_2' => 'nullable|numeric',
            'score_2_5' => 'nullable|numeric',
            'score_3' => 'nullable|numeric',
            'score_4' => 'nullable|numeric',
            'score_5' => 'nullable|numeric',
        ]);

        $data = $request->all();

        // 2. จัดการข้อมูลก่อนบันทึก
        // 2.1 กลุ่มที่อนุญาตให้ว่างเป็น null ได้ (เกณฑ์คะแนน)
        $scoreFields = ['score_0', 'score_1', 'score_2', 'score_2_5', 'score_3', 'score_4', 'score_5'];
        foreach ($scoreFields as $field) {
            $data[$field] = (isset($data[$field]) && $data[$field] !== '') ? $data[$field] : null;
        }

        // 2.2 กลุ่มที่ห้ามเป็น null ให้ตั้งค่าเป็น 0 (เป้าหมาย, น้ำหนัก)
        $requiredNumericFields = ['target_value', 'weight'];
        foreach ($requiredNumericFields as $field) {
            $data[$field] = (isset($data[$field]) && $data[$field] !== '') ? $data[$field] : 0;
        }

        // 3. สั่งอัปเดตข้อมูลทับข้อมูลเก่าในฐานข้อมูล
        $ranking->update($data);
        
        // Redirect กลับหน้าแสดงรายการพร้อมข้อความแจ้งเตือนสีเขียว (Success Indicator)
        return redirect()->to(session('user_url', route('rankings.index')))->with('success', 'แก้ไขข้อมูลตัวชี้วัดเรียบร้อยแล้ว');
    }

    /**
     * ลบตัวชี้วัดที่เลือกออกจากระบบ (Delete/Destroy)
     */
    public function destroy(Ranking $ranking)
    {
        // สั่งลบ Object ที่ระบุ
        $ranking->delete();
        
        // พาผู้ใช้กลับไปที่หน้าเดิมก่อนกดตกลงลบ
        return redirect()->back()->with('success', 'ลบข้อมูลตัวชี้วัดเรียบร้อยแล้ว');
    }
}