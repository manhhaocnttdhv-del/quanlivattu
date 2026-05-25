<?php

namespace App\Http\Controllers;

use App\Models\InventoryEntry;
use App\Models\InventoryEntryDetail;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryEntry::with(['warehouse', 'supplier', 'user'])->latest();

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
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $entries = $query->paginate(10)->appends($request->query());

        $warehouses = Auth::user()->role === 'Admin tổng' ? Warehouse::all() : collect();
        $suppliers = Supplier::all();

        return view('inventory_entries.index', compact('entries', 'warehouses', 'suppliers'));
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
        $materials = Material::with(['unit', 'warehouseStocks'])->get();

        return view('inventory_entries.create', compact('warehouses', 'suppliers', 'materials'));
    }

    public function store(Request $request)
    {
        if ($request->has('materials')) {
            $materials = $request->input('materials');
            foreach ($materials as $key => $item) {
                if (isset($item['unit_price']) && $item['unit_price'] !== '') {
                    $materials[$key]['unit_price'] = preg_replace('/\D/', '', $item['unit_price']);
                }
            }
            $request->merge(['materials' => $materials]);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'note' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|string',
            'materials.*.unit_price' => 'nullable|numeric|min:0',
            'materials.*.location' => 'nullable|string|max:100',
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
                    'unit_price' => $item['unit_price'] ?? 0,
                    'location' => $item['location'] ?? null,
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
                if ($detail->status === 'pending') {
                    $inventoryService->updateStock(
                        $inventoryEntry->warehouse_id,
                        $detail->material_id,
                        $detail->quantity,
                        'add',
                        $detail->unit_price,
                        $detail->location
                    );
                    $detail->update(['status' => 'approved']);
                }
            }

            $inventoryEntry->update(['status' => 'completed']);

            DB::commit();
            return back()->with('success', 'Đã duyệt toàn bộ phiếu nhập và cập nhật tồn kho!');
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

            // Reverse the stock addition only for details that have been approved
            foreach ($inventoryEntry->details as $detail) {
                if ($detail->status === 'approved') {
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

    /**
     * Duyệt từng dòng vật tư trong phiếu nhập
     */
    public function approveDetail(InventoryEntry $inventoryEntry, InventoryEntryDetail $detail, \App\Services\InventoryService $inventoryService)
    {
        if ($inventoryEntry->status !== 'pending') {
            return back()->with('error', 'Phiếu đã được xử lý xong, không thể duyệt thêm.');
        }
        if ($detail->status === 'approved') {
            return back()->with('error', 'Dòng vật tư này đã được duyệt rồi.');
        }

        try {
            DB::beginTransaction();

            // Cộng tồn kho cho dòng này
            $inventoryService->updateStock(
                $inventoryEntry->warehouse_id,
                $detail->material_id,
                $detail->quantity,
                'add',
                $detail->unit_price,
                $detail->location
            );

            $detail->update(['status' => 'approved']);

            // Nếu tất cả dòng đều approved -> chuyển phiếu thành completed
            $pendingCount = $inventoryEntry->details()->where('status', 'pending')->count();
            if ($pendingCount === 0) {
                $inventoryEntry->update(['status' => 'completed']);
            }

            DB::commit();
            return back()->with('success', 'Đã duyệt dòng vật tư và cộng tồn kho!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi duyệt: ' . $e->getMessage());
        }
    }

    /**
     * Xóa 1 dòng vật tư pending ra khỏi phiếu nhập
     */
    public function removeDetail(InventoryEntry $inventoryEntry, InventoryEntryDetail $detail)
    {
        if ($inventoryEntry->status !== 'pending') {
            return back()->with('error', 'Phiếu đã xử lý xong, không thể xóa dòng.');
        }
        if ($detail->status === 'approved') {
            return back()->with('error', 'Dòng đã duyệt, không thể xóa. Vui lòng hủy cả phiếu nếu cần.');
        }

        DB::transaction(function () use ($inventoryEntry, $detail) {
            $detail->delete();

            // Nếu phiếu hết dòng -> tự động hủy phiếu
            if ($inventoryEntry->details()->count() === 0) {
                $inventoryEntry->update(['status' => 'cancelled']);
            }
        });

        return back()->with('success', 'Đã xóa dòng vật tư khỏi phiếu!');
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
