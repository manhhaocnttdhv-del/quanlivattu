<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaterialsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        if (auth()->user()->role === 'Admin tổng') {
            return [
                'id',
                'ten_vat_tu',
                'mo_ta',
                'don_vi_tinh_id',
            ];
        }

        return [
            'id',
            'ten_vat_tu',
            'mo_ta',
            'don_vi_tinh_id',
            'gia_nhap',
            'gia_ban',
        ];
    }

    public function array(): array
    {
        if (auth()->user()->role === 'Admin tổng') {
            return [
                [null, 'Xi măng PCB40', 'Xi măng Hà Tiên PCB40', 1],
                [null, 'Thép phi 10', 'Thép cuộn phi 10mm', 2],
                [null, 'Gạch ống 4 lỗ', 'Gạch ống đất nung 4 lỗ', 3],
            ];
        }

        return [
            [null, 'Xi măng PCB40', 'Xi măng Hà Tiên PCB40', 1, 1850000, 2035000],
            [null, 'Thép phi 10', 'Thép cuộn phi 10mm', 2, 14500000, 15950000],
            [null, 'Gạch ống 4 lỗ', 'Gạch ống đất nung 4 lỗ', 3, 950, 1050],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $isAdminTong = auth()->user()->role === 'Admin tổng';
        $lastCol = $isAdminTong ? 'D' : 'F';

        // Style header row
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ]);

        // Thêm ghi chú hướng dẫn ở dòng cuối
        $lastRow = 5;
        $sheet->setCellValue('A' . $lastRow, '--- HƯỚNG DẪN ---');
        $sheet->setCellValue('A' . ($lastRow + 1), 'id: Để trống nếu tạo mới, nhập ID nếu muốn cập nhật vật tư đã có');
        $sheet->setCellValue('A' . ($lastRow + 2), 'ten_vat_tu: (Bắt buộc) Tên vật tư');
        $sheet->setCellValue('A' . ($lastRow + 3), 'mo_ta: Mô tả chi tiết vật tư');
        $sheet->setCellValue('A' . ($lastRow + 4), 'don_vi_tinh_id: ID đơn vị tính (xem danh sách ĐVT trong hệ thống)');
        
        if (!$isAdminTong) {
            $sheet->setCellValue('A' . ($lastRow + 5), 'gia_nhap: Giá nhập / Giá vốn (VNĐ)');
            $sheet->setCellValue('A' . ($lastRow + 6), 'gia_ban: Giá bán / Giá xuất kho (VNĐ)');
        }

        // Style ghi chú
        $sheet->getStyle('A' . $lastRow . ':A' . ($lastRow + ($isAdminTong ? 4 : 6)))->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '808080']],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        if (auth()->user()->role === 'Admin tổng') {
            return [
                'A' => 10,
                'B' => 30,
                'C' => 40,
                'D' => 18,
            ];
        }

        return [
            'A' => 10,
            'B' => 30,
            'C' => 40,
            'D' => 18,
            'E' => 18,
            'F' => 18,
        ];
    }
}
