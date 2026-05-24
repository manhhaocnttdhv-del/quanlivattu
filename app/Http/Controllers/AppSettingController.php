<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function index()
    {
        $settings = AppSetting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_min_stock_level' => 'required|numeric|min:0',
            'standard_work_days'      => 'required|integer|min:1|max:31',
        ]);

        foreach ($validated as $key => $value) {
            AppSetting::set($key, $value);
        }

        return back()->with('success', 'Đã lưu cài đặt thành công!');
    }
}
