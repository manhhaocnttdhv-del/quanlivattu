<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Lấy tất cả nhóm gốc kèm theo các nhóm con của chúng
        $roots = Category::with('children', 'parent', 'materials')
            ->whereNull('parent_id')
            ->latest()
            ->get();

        $allCategories = collect();
        foreach ($roots as $root) {
            $allCategories->push($root);
            foreach ($root->children as $child) {
                $allCategories->push($child);
            }
        }

        // Phân trang thủ công bộ sưu tập đã làm phẳng
        $page = request()->input('page', 1);
        $perPage = 15;
        $items = $allCategories->slice(($page - 1) * $perPage, $perPage)->values();
        $categories = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allCategories->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:categories,id',
        ], [
            'name.required' => 'Tên nhóm vật tư không được để trống.',
            'name.unique' => 'Tên nhóm vật tư đã tồn tại.',
        ]);

        // Không cho phép tạo sub-category của sub-category (chỉ 1 cấp)
        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            if ($parent && $parent->parent_id) {
                return back()->with('error', 'Chỉ hỗ trợ phân cấp 1 bậc (nhóm cha > nhóm con).')
                    ->withInput();
            }
        }

        Category::create($validated);
        return redirect()->route('categories.index')
            ->with('success', 'Thêm nhóm vật tư thành công!');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:categories,id',
        ], [
            'name.required' => 'Tên nhóm vật tư không được để trống.',
            'name.unique' => 'Tên nhóm vật tư đã tồn tại.',
        ]);

        // Không cho gán parent_id = chính nó
        if ($request->parent_id == $category->id) {
            return back()->with('error', 'Không thể gán nhóm cha là chính nó.')->withInput();
        }

        // Không cho phép tạo sub-category của sub-category
        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            if ($parent && $parent->parent_id) {
                return back()->with('error', 'Chỉ hỗ trợ phân cấp 1 bậc.')->withInput();
            }
        }

        // Nếu category này có children, không cho nó trở thành sub-category
        if ($request->parent_id && $category->children()->count() > 0) {
            return back()->with('error', 'Nhóm này đang có nhóm con, không thể chuyển thành nhóm con của nhóm khác.')
                ->withInput();
        }

        $category->update($validated);
        return redirect()->route('categories.index')
            ->with('success', 'Cập nhật nhóm vật tư thành công!');
    }

    public function destroy(Category $category)
    {
        // Kiểm tra có vật tư đang dùng không
        $materialCount = $category->materials()->count();
        $childMaterialCount = 0;

        foreach ($category->children as $child) {
            $childMaterialCount += $child->materials()->count();
        }

        $total = $materialCount + $childMaterialCount;

        if ($total > 0) {
            return redirect()->route('categories.index')
                ->with('error', "Không thể xóa! Nhóm này đang có {$total} vật tư được gán.");
        }

        // Xóa children trước
        $category->children()->delete();
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Xóa nhóm vật tư thành công!');
    }
}
