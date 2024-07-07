<?php

namespace App\Http\Controllers;

use App\Models\ItemOrderLimit;
use App\Models\Item;
use Illuminate\Http\Request;

class OrderLimitController extends Controller
{
    public function index()
    {
        $orderlimits = ItemOrderLimit::with('item')->get();
        return view('manager.orderLimit.index', compact('orderlimits'));
    }

    public function create()
    {
        $items = Item::all();
        return view('manager.orderLimit.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'itemId' => 'required',
            'orderLimit' => 'required|integer',
            'period' => 'required',
        ]);

        ItemOrderLimit::create($request->all());

        return redirect()->route('orderLimit.index')->with('success', 'Order limit added successfully.');
    }

    public function edit(ItemOrderLimit $orderlimit)
    {
        $items = Item::all();
        return view('manager.orderLimit.edit', compact('orderlimit', 'items'));
    }

    public function update(Request $request, ItemOrderLimit $orderlimit)
    {
        $request->validate([
            'itemId' => 'required',
            'orderLimit' => 'required|integer',
            'period' => 'required',
        ]);

        $orderlimit->update($request->all());

        return redirect()->route('orderlimits.index')->with('success', 'Order limit updated successfully.');
    }

    public function destroy($id)
    {
        ItemOrderLimit::destroy($id);
        return response()->json(['success' => true]);
    }
}
