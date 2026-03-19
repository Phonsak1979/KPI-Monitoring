<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Department::query();

        if ($search = $request->input('search')) {
            $query->where('department_name', 'like', "%{$search}%");
        }

        $departments = $query->paginate(50)->appends(['search' => $search]);
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'required',
        ]);

        Department::create($request->all());
        return redirect()->route('departments.index')->with('success', 'สร้างกลุ่มงาน/ฝ่ายเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'department_name' => 'required',
        ]);

        $department->update($request->all());
        return redirect()->route('departments.index')->with('success', 'อัปเดตกลุ่มงาน/ฝ่ายเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'ลบกลุ่มงาน/ฝ่ายเรียบร้อยแล้ว');
    }
}
