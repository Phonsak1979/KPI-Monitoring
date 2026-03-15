<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Hospital;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Hospital::query();

        if ($search = $request->input('search')) {
            $query->where('hospital_name', 'like', "%{$search}%")
                ->orWhere('hospital_code', 'like', "%{$search}%");
        }

        $hospitals = $query->orderBy('hospital_code', 'asc')->paginate(50)->appends(['search' => $search]);
        return view('hospitals.index', compact('hospitals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $districts = District::orderBy('district_code', 'asc')->get();
        return view('hospitals.create', compact('districts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hospital_code' => 'required',
            'hospital_name' => 'required',
            'district_id' => 'required',
        ]);

        Hospital::create($request->all());

        return redirect()->route('hospitals.index')->with('success', 'หน่วยงานถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hospital $hospital)
    {
        $hospital = Hospital::findOrFail($hospital->id);
        return view('hospitals.show', compact('hospital'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hospital $hospital)
    {
        $hospital = Hospital::findOrFail($hospital->id);
        $districts = District::orderBy('district_code', 'asc')->get();
        return view('hospitals.edit', compact('hospital', 'districts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hospital $hospital)
    {
        $request->validate([
            'hospital_code' => 'required',
            'hospital_name' => 'required',
            'district_id' => 'required',
        ]);

        $hospital->update($request->all());

        return redirect()->route('hospitals.index')->with('success', 'หน่วยบริการถูกแก้ไขเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hospital $hospital)
    {
        $hospital->delete();

        return redirect()->route('hospitals.index')->with('success', 'หน่วยบริการถูกลบเรียบร้อยแล้ว');
    }

    public function count()
    {
        $hospitals = Hospital::count();
        return view('hospitals.index', compact('hospitals'));
    }
}
