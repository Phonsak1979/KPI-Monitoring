<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
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
        $query = District::query();

        if ($search = $request->input('search')) {
            $query->where('district_name', 'like', "%{$search}%")
                ->orWhere('district_code', 'like', "%{$search}%");
        }

        $districts = $query->orderBy('district_code', 'asc')->paginate(50)->appends(['search' => $search]);
        return view('districts.index', compact('districts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('districts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'district_code' => 'required|string|max:255',
            'district_name' => 'required|string|max:255',
        ]);

        District::create($validated);

        return redirect()->route('districts.index')
            ->with('success', 'อำเภอถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(District $district)
    {
        return view('districts.show', compact('district'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(District $district)
    {
        return view('districts.edit', compact('district'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, District $district)
    {
        $validated = $request->validate([
            'district_code' => 'required|string|max:255',
            'district_name' => 'required|string|max:255',
        ]);

        $district->update($validated);

        return redirect()->route('districts.index')
            ->with('success', 'อำเภอถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        $district->delete();

        return redirect()->route('districts.index')
            ->with('success', 'อำเภอถูกลบเรียบร้อยแล้ว');
    }
}
