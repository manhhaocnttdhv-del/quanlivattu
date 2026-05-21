<?php

namespace App\Http\Controllers;

use App\Models\InventoryExit;
use App\Models\Warehouse;
use App\Models\Project;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryExitController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryExit::with(['warehouse', 'project', 'user'])->latest();

        if (Auth::user()->role !== 'Admin tổng') {
            $query->where('warehouse_id', Auth::user()->warehouse_id);
        }

        // Filters
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('warehouse_id') && Auth::user()->role === 'Admin tổng') {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $exits = $query->paginate(10)->appends($request->query());

        $warehouses = Auth::user()->role === 'Admin tổng' ? Warehouse::all() : collect();
        $projects = Project::all();

        return view('inventory_exits.index', compact('exits', 'warehouses', 'projects'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        if (Auth::user()->role !== 'Admin tổng') {
            $warehouses = $warehouses->where('id', Auth::user()->warehouse_id);
        }

        $projects = Auth::user()->role === 'Admin tổng'
            ? Project::all()
            : Project::where(function($q) {
                $q->where('warehouse_id', Auth::user()->warehouse_id)
                  ->orWhereNull('warehouse_id');
            })->get();
        $warehouseId = Auth::user()->role === 'Admin tổng' ? null : Auth::user()->warehouse_id;
        
        $materials = Material::with(['unit', 'warehouseStocks' => function($q) use ($warehouseId) {
            if ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            }
        }])->get();

        return view('inventory_exits.create', compact('warehouses', 'projects', 'materials'));
    }

    public function store(Request $request, \App\Services\InventoryService $inventoryService)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'project_id' => 'required|exists:projects,id',
            'note' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'materials.*.unit_price' => 'nullable|numeric|min:0',
            'materials.*.location' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $exit = InventoryExit::create([
                'date' => $validated['date'],
                'warehouse_id' => $validated['warehouse_id'],
                'project_id' => $validated['project_id'],
                'user_id' => Auth::id(),
                'status' => 'pending',
                'note' => $validated['note'],
            ]);

            foreach ($validated['materials'] as $item) {
                // Validate stock locally before allowing creation, even as pending
                $currentStock = $inventoryService->getStock($validated['warehouse_id'], $item['id']);
                if ($currentStock < $item['quantity']) {
                    throw new \Exception("Vật tư ID {$item['id']} không đủ tồn kho (hiện có: {$currentStock}, yêu cầu: {$item['quantity']}) tại kho này.");
                }

                // Kiểm tra Dự toán (BoQ) của Công trình
                $estimate = \App\Models\ProjectMaterial::where('project_id', $validated['project_id'])
                               ->where('material_id', $item['id'])->first();
                $estimatedQty = $estimate ? $estimate->estimated_quantity : 0;

                if ($estimatedQty <= 0) {
                    throw new \Exception("Vật tư ID {$item['id']} chưa được cấp định mức dự toán cho công trình này.");
                }

                // Tính tổng đã xuất (bao gồm cả các phiếu đã duyệt và đang chờ duyệt) để đối chiếu
                $alreadyExited = \App\Models\InventoryExitDetail::whereHas('inventoryExit', function($q) use ($validated) {
                    $q->where('project_id', $validated['project_id'])
                      ->whereIn('status', ['pending', 'completed']);
                })->where('material_id', $item['id'])->sum('quantity');

                if (($alreadyExited + $item['quantity']) > $estimatedQty) {
                    throw new \Exception("Vật tư ID {$item['id']} xuất vượt định mức! (Đã xuất+Chờ xuất: {$alreadyExited}, Yêu cầu thêm: {$item['quantity']}, Định mức: {$estimatedQty})");
                }

                $exit->details()->create([
                    'material_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'] ?? Material::find($item['id'])->selling_price ?? 0,
                    'location' => $item['location'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory-exits.index')->with('success', 'Tạo phiếu xuất thành công! Phiếu đang chờ duyệt.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function show(InventoryExit $inventoryExit)
    {
        $inventoryExit->load(['warehouse', 'project', 'user', 'details.material.unit']);
        return view('inventory_exits.show', compact('inventoryExit'));
    }

    public function approve(InventoryExit $inventoryExit, \App\Services\InventoryService $inventoryService)
    {
        if ($inventoryExit->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể duyệt phiếu đang chờ.');
        }

        try {
            DB::beginTransaction();

            foreach ($inventoryExit->details as $detail) {
                // Will throw exception if insufficient stock during transaction
                $inventoryService->updateStock(
                    $inventoryExit->warehouse_id,
                    $detail->material_id,
                    $detail->quantity,
                    'subtract',
                    null,
                    $detail->location
                );
            }

            $inventoryExit->update(['status' => 'completed']);

            DB::commit();
            return back()->with('success', 'Đã duyệt phiếu xuất và trừ tồn kho!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi duyệt: ' . $e->getMessage());
        }
    }

    public function cancel(InventoryExit $inventoryExit, \App\Services\InventoryService $inventoryService)
    {
        if ($inventoryExit->status === 'cancelled') {
            return back()->with('error', 'Phiếu đã bị hủy.');
        }

        try {
            DB::beginTransaction();

            // If it was completed, we need to reverse the stock subtraction by adding it back
            if ($inventoryExit->status === 'completed') {
                foreach ($inventoryExit->details as $detail) {
                    $inventoryService->updateStock(
                        $inventoryExit->warehouse_id,
                        $detail->material_id,
                        $detail->quantity,
                        'add'
                    );
                }
            }

            $inventoryExit->update(['status' => 'cancelled']);

            DB::commit();
            return back()->with('success', 'Đã hủy phiếu xuất thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi hủy: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        return redirect()->route('inventory-exits.index')->with('error', 'Không được sửa phiếu xuất.');
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        return redirect()->route('inventory-exits.index')->with('error', 'Vui lòng sử dụng tính năng Hủy Phiếu thay vì xóa vĩnh viễn.');
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\InventoryExitsExport(), 
            'danh-sach-phieu-xuat-' . date('Ymd-Hi') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $query = InventoryExit::with(['warehouse', 'project', 'user'])->latest();
        if (Auth::user()->role !== 'Admin tổng') {
            $query->where('warehouse_id', Auth::user()->warehouse_id);
        }
        $exits = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.inventory_exits_pdf', compact('exits'));
        
        return $pdf->download('danh-sach-phieu-xuat-' . date('Ymd-Hi') . '.pdf');
    }
}
