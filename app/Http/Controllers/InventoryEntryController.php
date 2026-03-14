<?php

namespace App\Http\Controllers;

use App\Models\InventoryEntry;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryEntryController extends Controller
{
    public function index()
    {
        $query = InventoryEntry::with(['warehouse', 'supplier', 'user'])->latest();
        if (Auth::user()->role !== 'Admin tổng') {
            $query->where('warehouse_id', Auth::user()->warehouse_id);
        }
        $entries = $query->paginate(10);
        return view('inventory_entries.index', compact('entries'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        
        // If not Admin tổng, only show their assigned warehouse
        if (Auth::user()->role !== 'Admin tổng') {
            $warehouses = $warehouses->where('id', Auth::user()->warehouse_id);
        }

        $suppliers = Auth::user()->role === 'Admin tổng'
            ? Supplier::all()
            : Supplier::where(function($q) {
                $q->where('warehouse_id', Auth::user()->warehouse_id)
                  ->orWhereNull('warehouse_id');
            })->get();
        $materials = Material::with('unit')->get();

        return view('inventory_entries.create', compact('warehouses', 'suppliers', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'note' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'materials.*.price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $entry = InventoryEntry::create([
                'date' => $validated['date'],
                'warehouse_id' => $validated['warehouse_id'],
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'status' => 'pending', 
                'note' => $validated['note'],
            ]);

            foreach ($validated['materials'] as $item) {
                $entry->details()->create([
                    'material_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? 0,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory-entries.index')->with('success', 'Tạo phiếu nhập thành công! Phiếu đang chờ duyệt.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function show(InventoryEntry $inventoryEntry)
    {
        $inventoryEntry->load(['warehouse', 'supplier', 'user', 'details.material.unit']);
        return view('inventory_entries.show', compact('inventoryEntry'));
    }

    public function approve(InventoryEntry $inventoryEntry, \App\Services\InventoryService $inventoryService)
    {
        if ($inventoryEntry->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể duyệt phiếu đang chờ.');
        }

        try {
            DB::beginTransaction();

            foreach ($inventoryEntry->details as $detail) {
                $inventoryService->updateStock(
                    $inventoryEntry->warehouse_id,
                    $detail->material_id,
                    $detail->quantity,
                    'add'
                );
            }

            $inventoryEntry->update(['status' => 'completed']);

            DB::commit();
            return back()->with('success', 'Đã duyệt phiếu nhập và cập nhật tồn kho!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi duyệt: ' . $e->getMessage());
        }
    }

    public function cancel(InventoryEntry $inventoryEntry, \App\Services\InventoryService $inventoryService)
    {
        if ($inventoryEntry->status === 'cancelled') {
            return back()->with('error', 'Phiếu đã bị hủy.');
        }

        try {
            DB::beginTransaction();

            // If it was completed, we need to reverse the stock addition
            if ($inventoryEntry->status === 'completed') {
                foreach ($inventoryEntry->details as $detail) {
                    $inventoryService->updateStock(
                        $inventoryEntry->warehouse_id,
                        $detail->material_id,
                        $detail->quantity,
                        'subtract'
                    );
                }
            }

            $inventoryEntry->update(['status' => 'cancelled']);

            DB::commit();
            return back()->with('success', 'Đã hủy phiếu nhập thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi hủy: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        return redirect()->route('inventory-entries.index')->with('error', 'Không được phép sửa phiếu nhập nữa.');
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        return redirect()->route('inventory-entries.index')->with('error', 'Vui lòng sử dụng tính năng Hủy Phiếu thay vì xóa vĩnh viễn.');
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\InventoryEntriesExport(), 
            'danh-sach-phieu-nhap-' . date('Ymd-Hi') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $query = InventoryEntry::with(['warehouse', 'supplier', 'user'])->latest();
        if (Auth::user()->role !== 'Admin tổng') {
            $query->where('warehouse_id', Auth::user()->warehouse_id);
        }
        $entries = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.inventory_entries_pdf', compact('entries'));
        
        return $pdf->download('danh-sach-phieu-nhap-' . date('Ymd-Hi') . '.pdf');
    }
}
