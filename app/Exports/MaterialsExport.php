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
        return Material::with('unit')->get();
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
        return [
            $material->id,
            $material->name,
            $material->description,
            $material->unit_id,
            $material->cost_price,
            $material->selling_price,
            $material->profit,
            $material->profit_margin . '%',
            $material->min_stock,
            $material->max_stock,
            $material->created_at ? $material->created_at->format('d/m/Y H:i') : '',
        ];
    }
}
