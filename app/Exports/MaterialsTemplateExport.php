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
        return [
            'id',
            'ten_vat_tu',
            'mo_ta',
            'don_vi_tinh_id',
            'ton_toi_thieu',
            'ton_toi_da',
        ];
    }

    public function array(): array
    {
        // Dữ liệu mẫu hướng dẫn
        return [
            [null, 'Xi măng PCB40', 'Xi măng Hà Tiên PCB40', 1, 100, 5000],
            [null, 'Thép phi 10', 'Thép cuộn phi 10mm', 2, 50, 2000],
            [null, 'Gạch ống 4 lỗ', 'Gạch ống đất nung 4 lỗ', 3, 1000, 50000],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:F1')->applyFromArray([
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
        $sheet->setCellValue('A' . ($lastRow + 5), 'ton_toi_thieu: Mức tồn kho tối thiểu (cảnh báo khi dưới mức này)');
        $sheet->setCellValue('A' . ($lastRow + 6), 'ton_toi_da: Mức tồn kho tối đa');

        // Style ghi chú
        $sheet->getStyle('A' . $lastRow . ':A' . ($lastRow + 6))->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '808080']],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
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
