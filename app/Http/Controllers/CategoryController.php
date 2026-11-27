<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->latest()->get();

        return view('admin.layouts.wrapper', [
            'content' => 'admin.category.index',
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.layouts.wrapper', [
            'content' => 'admin.category.form',
            'category' => new Category,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'stock' => 'required|integer|min:0',
        ]);

        Category::create($data);

        return redirect()->route('category.index')->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('admin.layouts.wrapper', [
            'content' => 'admin.category.form',
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'stock' => 'required|integer|min:0',
        ]);

        $category->update($data);

        return redirect()->route('category.index')->with('success', 'Kategori produk berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists() || $category->transactionItems()->exists()) {
            return redirect()->route('category.index')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan.');
        }

        $category->delete();

        return redirect()->route('category.index')->with('success', 'Kategori produk berhasil dihapus.');
    }
}
