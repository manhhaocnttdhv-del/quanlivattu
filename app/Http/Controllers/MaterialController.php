<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Unit;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with('unit', 'category');

        // Filter by name (search)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by unit
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $categoryId = $request->category_id;
            // Include children of selected category
            $categoryIds = \App\Models\Category::where('id', $categoryId)
                ->orWhere('parent_id', $categoryId)
                ->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'below_min') {
                $query->whereHas('warehouseStocks', function ($q) {
                    $q->whereRaw('stock < (SELECT min_stock FROM materials WHERE materials.id = material_warehouses.material_id)');
                });
            } elseif ($request->stock_status === 'above_max') {
                $query->whereHas('warehouseStocks', function ($q) {
                    $q->whereRaw('stock > (SELECT max_stock FROM materials WHERE materials.id = material_warehouses.material_id AND materials.max_stock > 0)');
                });
            }
        }

        $materials = $query->latest()->paginate(10)->appends($request->query());

        $units = Unit::all();
        $categories = \App\Models\Category::with('children')->whereNull('parent_id')->get();

        return view('materials.index', compact('materials', 'units', 'categories'));
    }

    public function create()
    {
        $units = Unit::all();
        $categories = \App\Models\Category::with('children')->whereNull('parent_id')->get();
        return view('materials.create', compact('units', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'category_id' => 'nullable|exists:categories,id',
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
        $categories = \App\Models\Category::with('children')->whereNull('parent_id')->get();
        return view('materials.edit', compact('material', 'units', 'categories'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'category_id' => 'nullable|exists:categories,id',
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

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MaterialsExport, 'danh-sach-vat-tu.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\MaterialsImport, $request->file('file'));
            return redirect()->route('materials.index')->with('success', 'Nhập dữ liệu vật tư thành công!');
        } catch (\Exception $e) {
            return redirect()->route('materials.index')->with('error', 'Lỗi nhập dữ liệu: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'mau-import-vat-tu.xlsx';
        $filePath = public_path('templates/' . $fileName);

        if (!file_exists($filePath)) {
            // Tạo file mẫu tự động nếu chưa tồn tại
            $export = new \App\Exports\MaterialsTemplateExport;
            return \Maatwebsite\Excel\Facades\Excel::download($export, $fileName);
        }

        return response()->download($filePath, $fileName);
    }
}
