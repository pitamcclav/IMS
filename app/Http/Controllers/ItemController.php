<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index()
    {

        if(auth()->user()->hasRole('admin')){
            $items = Item::with('inventory')->paginate(10);
        }else{
            $managerId = Auth::guard('staff')->user()->staffId;

            $storeId = Store::where('managerId', $managerId)
                ->value('storeId');

            $items = Item::with('inventory')->whereHas('category', function ($query) use ($storeId) {
                $query->where('storeId', $storeId);
            })->paginate(10);
        }
        return view('manager.item.index', compact('items'));
    }

    public function create()
    {

        if(auth()->user()->hasRole('admin')){
            $categories = Category::all();
        }
        else{
            $managerId = Auth::guard('staff')->user()->staffId;
            $storeId = Store::where('managerId', $managerId)
                ->value('storeId');
            $categories = Category::where('storeId', $storeId)->get();

        }
        return view('manager.item.create', compact('categories'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'itemName' => 'required',
            'categoryId' => 'required',
            'description' => 'nullable',
        ]);

        $item=Item::create($request->all());
        if ($request->ajax()) {
            return response()->json(['success' => true, 'item' => $item]);
        }
        return redirect()->route('item.index')->with('success', 'Item added successfully.');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('manager.item.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'itemName' => 'required',
            'categoryId' => 'required',
            'description' => 'nullable',
        ]);

        $item->update($request->all());

        return redirect()->route('item.index')->with('success', 'Item updated successfully.');
    }

    public function destroy($id)
    {
        $item = Item::find($id);
        $item->delete();
        return response()->json(['success' => true]);
    }
}
