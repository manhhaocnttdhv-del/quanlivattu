<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'Admin tổng') {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $logs = AuditLog::with('user')->latest()->paginate(20);

        return view('audit_logs.index', compact('logs'));
    }
}
