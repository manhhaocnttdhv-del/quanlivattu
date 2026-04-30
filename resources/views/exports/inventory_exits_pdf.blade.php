<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Danh sách Phiếu Xuất</title>
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
    <h2>DANH SÁCH PHIẾU XUẤT KHO</h2>
    <p><strong>Ngày xuất hệ thống:</strong> {{ date('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Mã Phiếu</th>
                <th>Ngày Xuất</th>
                <th>Kho Xuất</th>
                <th>Khách Hàng</th>
                <th>Người Lập</th>
                <th>Trạng Thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exits as $exit)
            <tr>
                <td class="text-center">PX-{{ str_pad($exit->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($exit->date)->format('d/m/Y') }}</td>
                <td>{{ $exit->warehouse->name ?? '' }}</td>
                <td>{{ $exit->project->name ?? '' }}</td>
                <td>{{ $exit->user->name ?? '' }}</td>
                <td class="text-center">
                    @if($exit->status == 'completed') Đã duyệt
                    @elseif($exit->status == 'pending') Chờ duyệt
                    @else Đã hủy @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
