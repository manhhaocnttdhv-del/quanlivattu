<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DeliveryPartnerController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('Xem danh sách vận chuyển');

        $query = DeliveryPartner::query();

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhere('driver_phone', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $partners = $query->latest()->paginate(10)->appends($request->query());

        return view('delivery_partners.index', compact('partners'));
    }

    public function create()
    {
        Gate::authorize('Quản lý đối tác vận chuyển');
        return view('delivery_partners.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('Quản lý đối tác vận chuyển');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:internal,external',
            'license_plate' => 'nullable|required_if:type,internal|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:50',
            'contact_name' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'note' => 'nullable|string',
        ], [
            'license_plate.required_if' => 'Biển số xe là bắt buộc đối với phương tiện vận chuyển nội bộ.'
        ]);

        DeliveryPartner::create($validated);

        return redirect()->route('delivery-partners.index')->with('success', 'Thêm phương tiện/đối tác vận chuyển thành công!');
    }

    public function edit(DeliveryPartner $deliveryPartner)
    {
        Gate::authorize('Quản lý đối tác vận chuyển');
        return view('delivery_partners.edit', compact('deliveryPartner'));
    }

    public function update(Request $request, DeliveryPartner $deliveryPartner)
    {
        Gate::authorize('Quản lý đối tác vận chuyển');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:internal,external',
            'license_plate' => 'nullable|required_if:type,internal|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:50',
            'contact_name' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'note' => 'nullable|string',
        ], [
            'license_plate.required_if' => 'Biển số xe là bắt buộc đối với phương tiện vận chuyển nội bộ.'
        ]);

        $deliveryPartner->update($validated);

        return redirect()->route('delivery-partners.index')->with('success', 'Cập nhật phương tiện/đối tác vận chuyển thành công!');
    }

    public function destroy(DeliveryPartner $deliveryPartner)
    {
        Gate::authorize('Quản lý đối tác vận chuyển');

        // Check if linked to any transaction
        $hasEntries = $deliveryPartner->inventoryEntries()->exists();
        $hasExits = $deliveryPartner->inventoryExits()->exists();
        $hasTransfers = $deliveryPartner->inventoryTransfers()->exists();

        if ($hasEntries || $hasExits || $hasTransfers) {
            // Do not delete, suggest deactivation instead
            return back()->with('error', 'Không thể xóa phương tiện/đối tác này vì đã có dữ liệu giao dịch liên kết. Hãy chuyển trạng thái sang "Ngừng hoạt động".');
        }

        $deliveryPartner->delete();

        return redirect()->route('delivery-partners.index')->with('success', 'Xóa phương tiện/đối tác vận chuyển thành công!');
    }
}
