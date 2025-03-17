<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    public function index()
    {
        if(Auth::guard('staff')->user()->hasRole('admin')){
            $categories = Category::all();
        }
        else{
            $managerId = Auth::guard('staff')->user()->staffId;
            $storeId = Store::where('managerId', $managerId)
                ->value('storeId');
            $categories = Category::where('storeId', $storeId)->get();

        }
        return view('manager.category.index', compact('categories'));
    }

    public function create()
    {
        return view('manager.category.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'categoryName' => 'required',
                'isReturnable' => 'required|boolean',
            ]);

            $categoryData = $request->only(['categoryName', 'isReturnable']);
            $manager = Auth::guard('staff')->user();
            $store = Store::where('managerId', $manager->staffId)->first();

            if(!$store) {
                throw new \Exception('You are not assigned to any store.');
            }

            $categoryData['storeId'] = $store->storeId;
            $category = Category::create($categoryData);

            if($request->wantsJson()) {
                return response()->json([
                    'message' => 'Category created successfully',
                    'category' => $category
                ]);
            }

            Session::flash('success', 'Category created successfully.');
            return redirect()->route('category.index');
        } catch (\Exception $e) {
            if($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 422);
            }

            Session::flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit(Category $category)
    {
        return view('manager.category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        try {
            $request->validate([
                'categoryName' => 'required',
                'isReturnable' => 'required|boolean',
            ]);

            $category->update($request->only(['categoryName', 'isReturnable']));

            if($request->wantsJson()) {
                return response()->json([
                    'message' => 'Category updated successfully',
                    'category' => $category
                ]);
            }

            Session::flash('success', 'Category updated successfully.');
            return redirect()->route('category.index');
        } catch (\Exception $e) {
            if($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 422);
            }

            Session::flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return response()->json([
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting category: ' . $e->getMessage()
            ], 422);
        }
    }
}
