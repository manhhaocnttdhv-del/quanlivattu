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
        $material = Material::find($row['id'] ?? null);
        
        if ($material) {
            $material->update([
                'name' => $row['ten_vat_tu'],
                'description' => $row['mo_ta'] ?? $material->description,
                'unit_id' => $row['don_vi_tinh_id'] ?? $material->unit_id,
                'min_stock' => $row['ton_toi_thieu'] ?? $material->min_stock,
                'max_stock' => $row['ton_toi_da'] ?? $material->max_stock,
            ]);
            return null;
        }

        return new Material([
            'name' => $row['ten_vat_tu'],
            'description' => $row['mo_ta'] ?? null,
            'unit_id' => $row['don_vi_tinh_id'] ?? 1, // Default unit
            'min_stock' => $row['ton_toi_thieu'] ?? 0,
            'max_stock' => $row['ton_toi_da'] ?? 0,
        ]);
    }
}
