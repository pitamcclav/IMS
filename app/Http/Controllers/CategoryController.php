<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('manager.category.index', compact('categories'));
    }

    public function create()
    {
        return view('manager.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoryName' => 'required',
            'isReturnable' => 'required|boolean',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')->with('success', 'Category added successfully.');
    }

    public function edit(Category $category)
    {
        return view('manager.category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'categoryName' => 'required',
            'isReturnable' => 'required|boolean',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
