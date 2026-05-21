<?php

namespace App\Exports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $warehouseId = auth()->user()->warehouse_id ?? \App\Models\Warehouse::first()->id ?? null;
        
        return Material::with(['unit', 'warehouseStocks' => function($q) use ($warehouseId) {
            if ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            }
        }])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên Vật Tư',
            'Mô Tả',
            'Đơn Vị Tính ID',
            'Giá Nhập (VNĐ)',
            'Giá Bán (VNĐ)',
            'Lợi Nhuận (VNĐ)',
            'Biên LN (%)',
            'Tồn Tối Thiểu',
            'Tồn Tối Đa',
            'Ngày Tạo',
        ];
    }

    public function map($material): array
    {
        $stockRecord = $material->warehouseStocks->first();
        $costPrice = $stockRecord ? $stockRecord->cost_price : 0;
        $sellingPrice = $stockRecord ? $stockRecord->selling_price : 0;
        $profit = $sellingPrice - $costPrice;
        $profitMargin = $costPrice > 0 ? round(($profit / $costPrice) * 100, 1) : 0;

        return [
            $material->id,
            $material->name,
            $material->description,
            $material->unit_id,
            $costPrice,
            $sellingPrice,
            $profit,
            $profitMargin . '%',
            $material->min_stock,
            $material->max_stock,
            $material->created_at ? $material->created_at->format('d/m/Y H:i') : '',
        ];
    }
}
