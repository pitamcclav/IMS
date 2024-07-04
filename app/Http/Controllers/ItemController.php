<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('inventory')->paginate(10);
        return view('manager.item.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
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

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}
