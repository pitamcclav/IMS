<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Store;
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

        $categoryData = $request->all();

        // Get the currently authenticated manager
        $manager = auth()->user();
        $store = Store::where('staffId', $manager->staffId)->first();


        // Check if the manager is attached to a store
        if ($manager && $store->storeId) {
            // Set the store ID of the category to the store ID of the manager
            $categoryData['storeId'] = $store->storeId;
        }

        Category::create($categoryData);

        Session::flash('success', 'Category added successfully.');

        return redirect()->route('category.index');
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

    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();
        return response()->json(['success' => true]);
    }
}
