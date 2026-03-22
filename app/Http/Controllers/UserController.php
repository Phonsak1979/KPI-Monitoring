<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        // Restrict all user management routes to admin only, except the profile edit methods
        $this->middleware('admin')->except(['profileEdit', 'profileUpdate']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->orderBy('id', 'asc')->paginate(50)->appends(['search' => $search]);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->route('users.index')->with('success', 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if ($user->id === 1 && auth()->id() !== 1) {
            return redirect()->route('users.index')->with('error', 'คุณไม่สามารถแก้ไขข้อมูลของผู้ดูแลระบบหลักได้');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($user->id === 1 && auth()->id() !== 1) {
            return redirect()->route('users.index')->with('error', 'คุณไม่สามารถแก้ไขข้อมูลของผู้ดูแลระบบหลักได้');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $data = $request->except(['password']);

        if ($request->filled('password')) {
            $request->validate(['password' => ['string', 'min:8']]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'แก้ไขข้อมูลผู้ใช้งานเรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function profileEdit()
    {
        $user = auth()->user();
        return view('users.detail', compact('user'));
    }

    /**
     * Update the user's profile in storage.
     */
    public function profileUpdate(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $request->validate(['password' => ['string', 'min:8']]);
            $data['password'] = Hash::make($request->password);
        }

        /** @var \App\Models\User $user */
        $user->update($data);

        return redirect()->route('user.detail')->with('success', 'อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('users.index')->with('error', 'ไม่สามารถลบผู้ดูแลระบบหลักได้');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'ผู้ใช้งานถูกลบเรียบร้อยแล้ว');
    }

    /**
     * Update user status via AJAX toggle mechanism.
     */
    public function changeStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        
        if ($user->id === 1) {
            return response()->json(['message' => 'ไม่สามารถเปลี่ยนสถานะผู้ดูแลระบบหลักได้'], 403);
        }

        $user->status = $request->status;
        $user->save();

        return response()->json(['message' => 'อัปเดตสถานะสำเร็จ']);
    }
}
