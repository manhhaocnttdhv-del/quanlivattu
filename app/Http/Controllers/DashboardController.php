<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Material;
use App\Models\InventoryEntry;
use App\Models\InventoryExit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_warehouses' => Warehouse::count(),
            'total_materials' => Material::count(),
            'total_entries' => InventoryEntry::count(),
            'total_exits' => InventoryExit::count(),
        ];

        // Basic recent activity
        $recent_entries = InventoryEntry::with('warehouse', 'supplier')->latest()->take(5)->get();
        $recent_exits = InventoryExit::with('warehouse', 'customer')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recent_entries', 'recent_exits'));
    }
}
