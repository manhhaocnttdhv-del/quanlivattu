<?php

namespace App\Exports;

use App\Models\InventoryExit;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryExitsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = InventoryExit::with(['warehouse', 'customer', 'user'])->latest();
        
        if (Auth::user()->role !== 'Admin tổng') {
            $query->where('warehouse_id', Auth::user()->warehouse_id);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Mã Phiếu Xuất',
            'Ngày Xuất',
            'Kho Xuất',
            'Khách Hàng',
            'Người Tạo',
            'Trạng Thái',
            'Ghi Chú',
        ];
    }

    public function map($exit): array
    {
        $statusMap = [
            'pending' => 'Chờ duyệt',
            'completed' => 'Đã duyệt',
            'cancelled' => 'Đã hủy',
        ];

        return [
            'PX-' . str_pad($exit->id, 5, '0', STR_PAD_LEFT),
            \Carbon\Carbon::parse($exit->date)->format('d/m/Y'),
            $exit->warehouse->name ?? 'N/A',
            $exit->customer->name ?? 'N/A',
            $exit->user->name ?? 'N/A',
            $statusMap[$exit->status] ?? $exit->status,
            $exit->note,
        ];
    }
}
