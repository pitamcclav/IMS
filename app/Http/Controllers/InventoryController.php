<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Colour;
use App\Models\Size;
use App\Models\Supplier;
use App\Models\Supply;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        // Eager load related models
        $inventories = Inventory::with(['item', 'colour', 'size'])->get();
//        print($inventories);
        return view('manager.inventory.index', compact('inventories'));
    }

    public function create()
    {
        $items = Item::all();
        $colors = Colour::all();
        $sizes = Size::all();
        $suppliers = Supplier::all();
        $categories = Category::all();
        return view('manager.inventory.create', compact('items', 'colors', 'sizes','suppliers','categories'));
    }

    public function store(Request $request)
    {
        print($request);

        $request->validate([
            'itemid' => 'required',
            'colourid' => 'required',
            'sizeid' => 'required',
            'quantity' => 'required|integer',
        ]);

        $item = Item::find($request->itemid);
        $category = $item->category;
        $storeId = $category->storeId;


        // Find the existing inventory item or create a new one
        $inventory = Inventory::where('itemid', $request->itemid)
            ->where('colourid', $request->colourid)
            ->where('sizeid', $request->sizeid)
            ->first();

        if ($inventory) {
            // Update the existing inventory item
            $inventory->quantity += $request->quantity;
            $inventory->initialQuantity = $inventory->quantity;
        } else {
            // Create a new inventory item
            $inventory = Inventory::create([
                'itemId' => $request->itemid,
                'colourId' => $request->colourid,
                'sizeId' => $request->sizeid,
                'quantity' => $request->quantity,
                'initialQuantity' => $request->quantity,
                'storeId' => $storeId,
            ]);
        }

        $inventory->save();

        Supply::create([
            'supplierId'=>$request->supplierid,
            'itemId'=>$request->itemid,
            'supplyDate'=>Carbon::now(),
            'quantity'=>$request->quantity,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Inventory item added successfully.');
    }

    public function storeColor(Request $request)
    {
        $color = Colour::create([
            'colourName' => $request->colorName,
        ]);

        return response()->json(['success' => true, 'color' => $color]);
    }

    public function storeSize(Request $request)
    {
        $size = Size::create([
            'sizeValue' => $request->sizeValue,
        ]);

        return response()->json(['success' => true, 'size' => $size]);
    }


    public function edit(Inventory $inventory)
    {
        $items = Item::all();
        $colors = Colour::all();
        $sizes = Size::all();
        return view('manager.inventory.edit', compact('inventory', 'items', 'colors', 'sizes'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'itemid' => 'required',
            'colourid' => 'required',
            'sizeid' => 'required',
            'quantity' => 'required|integer',
            'initialQuantity' => 'required|integer',
        ]);

        $inventory->update($request->all());

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully.');
    }
}
