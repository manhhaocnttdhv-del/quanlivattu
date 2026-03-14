<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Báo cáo Tồn Kho</title>
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
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>BÁO CÁO TỒN KHO</h2>
    <p><strong>Ngày xuất biên bản:</strong> {{ date('d/m/Y H:i') }}</p>
    <p><strong>Kho hàng:</strong> {{ $warehouseName }}</p>

    <table>
        <thead>
            <tr>
                <th>Mã VT</th>
                <th>Tên Vật tư</th>
                <th>ĐVT</th>
                <th>Kho hàng</th>
                <th class="text-right">Tồn hiện tại</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockData as $stock)
            <tr>
                <td class="text-center">{{ $stock->material->id }}</td>
                <td>{{ $stock->material->name }}</td>
                <td class="text-center">{{ $stock->material->unit->name }}</td>
                <td>{{ $stock->warehouse->name }}</td>
                <td class="text-right">{{ $stock->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
