<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryAlert;

class InventoryAlertController extends Controller
{
    public function index()
    {
        $alerts = InventoryAlert::with('material')->orderBy('is_resolved')->latest()->paginate(15);
        return view('inventory_alerts.index', compact('alerts'));
    }

    public function resolve(InventoryAlert $inventoryAlert)
    {
        $inventoryAlert->update(['is_resolved' => true]);
        return back()->with('success', 'Đã đánh dấu xử lý cảnh báo này.');
    }
}
