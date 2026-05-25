<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $query = Project::with('warehouse')->latest();

        if (auth()->check() && auth()->user()->role !== 'Admin tổng') {
            $query->where('warehouse_id', auth()->user()->warehouse_id)->orWhereNull('warehouse_id');
        }

        $projects = $query->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $warehouses = [];
        if (auth()->check() && auth()->user()->role === 'Admin tổng') {
            $warehouses = \App\Models\Warehouse::all();
        }
        return view('projects.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:projects',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ], [
            'name.required' => 'Tên công trình không được để trống.',
            'name.unique' => 'Tên công trình đã tồn tại.',
        ]);

        if (auth()->user()->role !== 'Admin tổng') {
            $validated['warehouse_id'] = auth()->user()->warehouse_id;
        }

        Project::create($validated);
        return redirect()->route('projects.index')->with('success', 'Thêm công trình thành công!');
    }

    public function show(Project $project)
    {
        $project->load('projectMaterials.material.unit');
        $materials = \App\Models\Material::with('unit')->get();
        return view('projects.show', compact('project', 'materials'));
    }

    public function updateMaterials(Request $request, Project $project)
    {
        // Require admin privileges
        if (!auth()->user()->isAdminTong() && !auth()->user()->isAdminKho()) {
            abort(403, 'Bạn không có quyền cập nhật dự toán công trình.');
        }

        $request->validate([
            'materials' => 'array',
            'materials.*' => 'nullable|string' // id material => quantity
        ]);

        foreach ($request->input('materials', []) as $materialId => $quantity) {
            if ($quantity > 0) {
                \App\Models\ProjectMaterial::updateOrCreate(
                    ['project_id' => $project->id, 'material_id' => $materialId],
                    ['estimated_quantity' => $quantity]
                );
            } else {
                \App\Models\ProjectMaterial::where('project_id', $project->id)->where('material_id', $materialId)->delete();
            }
        }

        return redirect()->back()->with('success', 'Đã cập nhật Bảng định mức dự toán công trình!');
    }

    public function edit(Project $project)
    {
        $warehouses = [];
        if (auth()->check() && auth()->user()->role === 'Admin tổng') {
            $warehouses = \App\Models\Warehouse::all();
        }
        return view('projects.edit', compact('project', 'warehouses'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:projects,name,' . $project->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ], [
            'name.required' => 'Tên công trình không được để trống.',
            'name.unique' => 'Tên công trình đã tồn tại.',
        ]);

        if (auth()->user()->role !== 'Admin tổng') {
            $validated['warehouse_id'] = auth()->user()->warehouse_id;
        }

        $project->update($validated);
        return redirect()->route('projects.index')->with('success', 'Cập nhật công trình thành công!');
    }

    public function destroy(Project $project)
    {
        // Add check if project is used in inventory exits before deletion
        if ($project->inventoryExits()->count() > 0) {
            return redirect()->route('projects.index')->with('error', 'Không thể xóa công trình đã có giao dịch!');
        }
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Xóa công trình thành công!');
    }
}
