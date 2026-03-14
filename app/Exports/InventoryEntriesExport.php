<?php

namespace App\Exports;

use App\Models\InventoryEntry;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryEntriesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = InventoryEntry::with(['warehouse', 'supplier', 'user'])->latest();
        
        if (Auth::user()->role !== 'Admin tổng') {
            $query->where('warehouse_id', Auth::user()->warehouse_id);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Mã Phiếu Nhập',
            'Ngày Nhập',
            'Kho Nhập',
            'Nhà Cung Cấp',
            'Người Tạo',
            'Trạng Thái',
            'Ghi Chú',
        ];
    }

    public function map($entry): array
    {
        $statusMap = [
            'pending' => 'Chờ duyệt',
            'completed' => 'Đã duyệt',
            'cancelled' => 'Đã hủy',
        ];

        return [
            'PN-' . str_pad($entry->id, 5, '0', STR_PAD_LEFT),
            \Carbon\Carbon::parse($entry->date)->format('d/m/Y'),
            $entry->warehouse->name ?? 'N/A',
            $entry->supplier->name ?? 'N/A',
            $entry->user->name ?? 'N/A',
            $statusMap[$entry->status] ?? $entry->status,
            $entry->note,
        ];
    }
}
