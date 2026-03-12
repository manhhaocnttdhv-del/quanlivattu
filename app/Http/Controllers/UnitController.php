<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->paginate(10);
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units',
        ], [
            'name.required' => 'Tên đơn vị tính không được để trống.',
            'name.unique' => 'Tên đơn vị tính đã tồn tại.',
        ]);

        Unit::create($validated);
        return redirect()->route('units.index')->with('success', 'Thêm đơn vị tính thành công!');
    }

    public function show(Unit $unit)
    {
        return view('units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
        ], [
            'name.required' => 'Tên đơn vị tính không được để trống.',
            'name.unique' => 'Tên đơn vị tính đã tồn tại.',
        ]);

        $unit->update($validated);
        return redirect()->route('units.index')->with('success', 'Cập nhật đơn vị tính thành công!');
    }

    public function destroy(Unit $unit)
    {
        // Add check if unit is used in materials before deletion
        if ($unit->materials()->count() > 0) {
            return redirect()->route('units.index')->with('error', 'Không thể xóa đơn vị tính đang được sử dụng!');
        }
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Xóa đơn vị tính thành công!');
    }
}
