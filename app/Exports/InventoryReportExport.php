<?php

namespace App\Exports;

use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $warehouseId;

    public function __construct($warehouseId = null)
    {
        $this->warehouseId = $warehouseId;
    }

    public function collection()
    {
        $query = DB::table('material_warehouse')
            ->join('materials', 'material_warehouse.material_id', '=', 'materials.id')
            ->join('warehouses', 'material_warehouse.warehouse_id', '=', 'warehouses.id')
            ->join('units', 'materials.unit_id', '=', 'units.id')
            ->select(
                'materials.id',
                'materials.name as material_name',
                'units.name as unit_name',
                'warehouses.name as warehouse_name',
                'material_warehouse.stock',
                'materials.min_stock'
            );

        if ($this->warehouseId) {
            $query->where('material_warehouse.warehouse_id', $this->warehouseId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID Vật tư',
            'Tên Vật tư',
            'Đơn vị tính',
            'Kho hàng',
            'Tồn kho hiện tại',
            'Định mức tối thiểu',
            'Trạng thái',
        ];
    }

    public function map($row): array
    {
        $status = $row->stock < $row->min_stock ? 'Sắp hết' : 'An toàn';
        return [
            $row->id,
            $row->material_name,
            $row->unit_name,
            $row->warehouse_name,
            $row->stock,
            $row->min_stock,
            $status,
        ];
    }
}
