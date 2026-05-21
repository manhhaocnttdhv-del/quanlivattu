<?php

namespace App\Imports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaterialsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip if name is missing
        if (!isset($row['ten_vat_tu'])) {
            return null;
        }

        // Try to update existing or create new
        $user = auth()->user();
        $isWarehouseAdmin = $user && $user->role === 'Admin kho';
        $warehouseId = $isWarehouseAdmin ? $user->warehouse_id : null;
        $material = Material::find($row['id'] ?? null);
        
        if ($material) {
            $material->update([
                'name' => $row['ten_vat_tu'],
                'description' => $row['mo_ta'] ?? $material->description,
                'unit_id' => $row['don_vi_tinh_id'] ?? $material->unit_id,
            ]);
            
            if ($warehouseId) {
                \App\Models\MaterialWarehouse::updateOrCreate(
                    ['warehouse_id' => $warehouseId, 'material_id' => $material->id],
                    [
                        'cost_price' => $row['gia_nhap'] ?? 0,
                        'selling_price' => $row['gia_ban'] ?? 0,
                    ]
                );
            }
            return null;
        }

        $newMaterial = Material::create([
            'name' => $row['ten_vat_tu'],
            'description' => $row['mo_ta'] ?? null,
            'unit_id' => $row['don_vi_tinh_id'] ?? 1,
        ]);

        if ($warehouseId) {
            \App\Models\MaterialWarehouse::create([
                'warehouse_id' => $warehouseId,
                'material_id' => $newMaterial->id,
                'stock' => 0,
                'average_cost' => 0,
                'cost_price' => $row['gia_nhap'] ?? 0,
                'selling_price' => $row['gia_ban'] ?? 0,
            ]);
        }

        return $newMaterial;
    }
}
