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
        if (auth()->user()->role === 'Admin tổng') {
            return Material::with('unit')->get();
        }

        $warehouseId = auth()->user()->warehouse_id ?? \App\Models\Warehouse::first()->id ?? null;
        
        return Material::with(['unit', 'warehouseStocks' => function($q) use ($warehouseId) {
            if ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            }
        }])->get();
    }

    public function headings(): array
    {
        if (auth()->user()->role === 'Admin tổng') {
            return [
                'ID',
                'Tên Vật Tư',
                'Mô Tả',
                'Đơn Vị Tính ID',
                'Ngày Tạo',
            ];
        }

        return [
            'ID',
            'Tên Vật Tư',
            'Mô Tả',
            'Đơn Vị Tính ID',
            'Giá Nhập (VNĐ)',
            'Giá Bán (VNĐ)',
            'Lợi Nhuận (VNĐ)',
            'Biên LN (%)',
            'Ngày Tạo',
        ];
    }

    public function map($material): array
    {
        if (auth()->user()->role === 'Admin tổng') {
            return [
                $material->id,
                $material->name,
                $material->description,
                $material->unit_id,
                $material->created_at ? $material->created_at->format('d/m/Y H:i') : '',
            ];
        }

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
            $material->created_at ? $material->created_at->format('d/m/Y H:i') : '',
        ];
    }
}
