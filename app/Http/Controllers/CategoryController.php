<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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

        Session::flash('success', 'Category added successfully.');

        return redirect()->route('categories.index');
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

        Session::flash('success', 'Category updated successfully.');

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
