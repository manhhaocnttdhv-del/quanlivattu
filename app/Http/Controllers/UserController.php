<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('warehouse')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        return view('users.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Admin tổng,Admin kho,Nhân viên kho',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);
        return redirect()->route('users.index')->with('success', 'Thêm người dùng thành công!');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        return view('users.edit', compact('user', 'warehouses'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:Admin tổng,Admin kho,Nhân viên kho',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'status' => 'required|in:active,inactive',
        ];

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8';
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Không thể tự xóa tài khoản đang đăng nhập!');
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Xóa người dùng thành công!');
    }
}
