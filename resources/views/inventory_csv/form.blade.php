@extends('layouts.admin')

@section('title', 'Import CSV Phiếu nhập / xuất')
@section('header', 'Import CSV Phiếu nhập / xuất')

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-cloud-upload"></i> Tải file CSV</h5>
      </div>
      <div class="card-body">
        @if (session('success'))
          <div class="alert alert-success" style="white-space:pre-line">{{ session('success') }}</div>
        @endif
        @if (session('error'))
          <div class="alert alert-danger" style="white-space:pre-line">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('inventory-csv.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label">Loại phiếu</label>
            <select name="type" class="form-select" required>
              <option value="entry">Phiếu nhập kho</option>
              <option value="exit">Phiếu xuất kho</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">File CSV</label>
            <input type="file" name="file" accept=".csv,.txt" class="form-control" required>
            <small class="text-muted">Tối đa 200MB / lần upload qua web. File lớn hơn → dùng CLI bên dưới.</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Chunk size (số phiếu / lô)</label>
            <input type="number" name="chunk" value="2000" min="100" max="10000" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-upload"></i> Import
          </button>
          <a href="{{ route('inventory-csv.template', ['type' => 'entry']) }}" class="btn btn-outline-secondary">
            <i class="bi bi-download"></i> Tải mẫu phiếu nhập
          </a>
          <a href="{{ route('inventory-csv.template', ['type' => 'exit']) }}" class="btn btn-outline-secondary">
            <i class="bi bi-download"></i> Tải mẫu phiếu xuất
          </a>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4 d-none">
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Hướng dẫn</h6>
      </div>
      <div class="card-body small">
        <p><strong>Cấu trúc CSV</strong> (1 dòng = 1 chi tiết, các dòng cùng phiếu đặt cùng <code>external_id</code>):</p>
        <pre class="bg-light p-2 rounded" style="font-size:11px;white-space:pre-wrap">external_id,date,warehouse_id,partner_id,user_id,status,note,delivery_partner_id,delivery_status,delivery_fee,delivery_code,material_id,quantity,unit_price,batch_code,expiry_date,location</pre>
        <ul class="mb-2">
          <li><code>partner_id</code> = supplier_id (nếu nhập) hoặc project_id (nếu xuất).</li>
          <li><code>external_id</code> dùng để gom dòng cùng phiếu. Ví dụ 3 dòng có external_id = 1 → 1 phiếu, 3 chi tiết.</li>
          <li><code>date</code> định dạng <code>YYYY-MM-DD</code>.</li>
          <li>Để trống <code>delivery_partner_id</code> nếu không dùng đối tác vận chuyển.</li>
        </ul>

        <hr>
        <p><strong>File CỰC LỚN (&gt; 200MB / vài triệu dòng)</strong> — chạy CLI:</p>
        <pre class="bg-dark text-white p-2 rounded" style="font-size:11px;white-space:pre-wrap">php artisan inventory:import-csv path/to/file.csv --type=entry --chunk=2000 --no-progress</pre>
        <p class="text-muted">Streaming bằng <code>fgetcsv</code>, không nạp cả file vào RAM → file 10GB vẫn xử lý được, chỉ phụ thuộc tốc độ ghi DB.</p>
      </div>
    </div>
  </div>
</div>
@endsection
