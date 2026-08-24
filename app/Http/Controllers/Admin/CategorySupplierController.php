<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategorySupplier;
use Illuminate\Http\Request;

class CategorySupplierController extends Controller
{
    public function index()
    {
        $categories = CategorySupplier::orderBy('created_at', 'desc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100',
        ]);

        CategorySupplier::create($request->only('category_name'));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(CategorySupplier $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, CategorySupplier $category)
    {
        $request->validate([
            'category_name' => 'required|string|max:100',
        ]);

        $category->update($request->only('category_name'));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(CategorySupplier $category)
    {
        $category->delete();
        return back()->with('success', 'Categoría eliminada correctamente.');
    }
}
