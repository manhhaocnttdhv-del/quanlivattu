<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Danh sách Phiếu Nhập</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>DANH SÁCH PHIẾU NHẬP KHO</h2>
    <p><strong>Ngày xuất hệ thống:</strong> {{ date('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Mã Phiếu</th>
                <th>Ngày Nhập</th>
                <th>Kho Nhập</th>
                <th>Nhà Cung Cấp</th>
                <th>Ghi chú</th>
                <th>Người Lập</th>
                <th>Trạng Thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
            <tr>
                <td class="text-center">PN-{{ str_pad($entry->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}</td>
                <td>{{ $entry->warehouse->name ?? '' }}</td>
                <td>{{ $entry->supplier->name ?? '' }}</td>
                <td>{{ $entry->note ?? '' }}</td>
                <td>{{ $entry->user->name ?? '' }}</td>
                <td class="text-center">
                    @if($entry->status == 'completed') Đã duyệt
                    @elseif($entry->status == 'pending') Chờ duyệt
                    @else Đã hủy @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
