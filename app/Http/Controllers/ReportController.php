<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function inventory()
    {
        // Simple stock calculation for basic reporting
        $materials = Material::with('unit')->get()->map(function ($material) {
            $totalIn = DB::table('inventory_entry_details')
                ->join('inventory_entries', 'inventory_entries.id', '=', 'inventory_entry_details.inventory_entry_id')
                ->where('inventory_entries.status', 'completed')
                ->where('material_id', $material->id)
                ->sum('quantity');

            $totalOut = DB::table('inventory_exit_details')
                ->join('inventory_exits', 'inventory_exits.id', '=', 'inventory_exit_details.inventory_exit_id')
                ->where('inventory_exits.status', 'completed')
                ->where('material_id', $material->id)
                ->sum('quantity');

            $material->stock = $totalIn - $totalOut;
            $material->total_in = $totalIn;
            $material->total_out = $totalOut;

            return $material;
        });

        return view('reports.inventory', compact('materials'));
    }
}
