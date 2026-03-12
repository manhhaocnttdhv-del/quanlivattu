<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Unit;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with('unit')->latest()->paginate(10);
        return view('materials.index', compact('materials'));
    }

    public function create()
    {
        $units = Unit::all();
        return view('materials.create', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'min_stock' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
        ]);

        Material::create($validated);
        return redirect()->route('materials.index')->with('success', 'Thêm vật tư thành công!');
    }

    public function show(Material $material)
    {
        return view('materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        $units = Unit::all();
        return view('materials.edit', compact('material', 'units'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'min_stock' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
        ]);

        $material->update($validated);
        return redirect()->route('materials.index')->with('success', 'Cập nhật vật tư thành công!');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Xóa vật tư thành công!');
    }
}
