<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // 1. Determine selected warehouse
        if ($user && $user->role === 'Admin kho') {
            $selectedWarehouseId = $user->warehouse_id;
        } else {
            $selectedWarehouseId = $request->input('kho') ?: (Warehouse::first()->id ?? null);
        }

        // 2. Base query with eager loading for unit, category, and ONLY the selected warehouse's stock
        $query = Material::with(['unit', 'category', 'warehouseStocks' => function ($q) use ($selectedWarehouseId) {
            $q->where('warehouse_id', $selectedWarehouseId);
        }]);

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

        // Filter by stock status (using the selected warehouse's stock)
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'below_min') {
                $query->where(function($q) use ($selectedWarehouseId) {
                    $q->whereHas('warehouseStocks', function ($sub) use ($selectedWarehouseId) {
                        $sub->where('warehouse_id', $selectedWarehouseId)
                            ->whereRaw('stock < (SELECT min_stock FROM materials WHERE materials.id = material_warehouses.material_id)');
                    })->orWhereDoesntHave('warehouseStocks', function($sub) use ($selectedWarehouseId) {
                        $sub->where('warehouse_id', $selectedWarehouseId);
                    })->where('min_stock', '>', 0);
                });
            } elseif ($request->stock_status === 'above_max') {
                $query->whereHas('warehouseStocks', function ($sub) use ($selectedWarehouseId) {
                    $sub->where('warehouse_id', $selectedWarehouseId)
                        ->whereRaw('stock > (SELECT max_stock FROM materials WHERE materials.id = material_warehouses.material_id AND materials.max_stock > 0)');
                });
            }
        }

        $materials = $query->latest()->paginate(10)->appends($request->query());

        $units = Unit::all();
        $categories = \App\Models\Category::with('children')->whereNull('parent_id')->get();
        $warehouses = Warehouse::where('status', 'active')->get();

        return view('materials.index', compact('materials', 'units', 'categories', 'warehouses', 'selectedWarehouseId'));
    }

    public function updateStock(Request $request, \App\Services\InventoryService $inventoryService)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'stock' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:100',
        ]);

        try {
            $warehouseId = $validated['warehouse_id'];
            $materialId = $validated['material_id'];
            $newStock = $validated['stock'];
            $costPrice = $validated['cost_price'] ?: 0;
            $sellingPrice = $validated['selling_price'] ?: 0;
            $location = $validated['location'];

            $currentRecord = \App\Models\MaterialWarehouse::where('warehouse_id', $warehouseId)
                ->where('material_id', $materialId)
                ->first();
            
            $currentStock = $currentRecord ? $currentRecord->stock : 0;
            $diff = $newStock - $currentStock;

            // Make sure the record exists or is created
            $record = \App\Models\MaterialWarehouse::firstOrCreate(
                ['warehouse_id' => $warehouseId, 'material_id' => $materialId],
                ['stock' => 0, 'average_cost' => 0]
            );

            // Update cost_price and selling_price
            $record->cost_price = $costPrice;
            $record->selling_price = $sellingPrice;
            if ($location) {
                $record->location = $location;
            }

            if ($diff > 0) {
                $inventoryService->updateStock($warehouseId, $materialId, $diff, 'add', $costPrice, $location);
            } elseif ($diff < 0) {
                $inventoryService->updateStock($warehouseId, $materialId, abs($diff), 'subtract', null, $location);
            } else {
                $record->save();
            }

            // Ensure the specific prices are saved to the database record
            $finalRecord = \App\Models\MaterialWarehouse::where('warehouse_id', $warehouseId)
                ->where('material_id', $materialId)
                ->first();
            if ($finalRecord) {
                $finalRecord->cost_price = $costPrice;
                $finalRecord->selling_price = $sellingPrice;
                $finalRecord->save();
            }

            return redirect()->back()->with('success', 'Cập nhật tồn kho và giá thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
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
