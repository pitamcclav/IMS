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
use Illuminate\Support\Facades\Session;

class InventoryController extends Controller
{
    public function index()
    {
        // Eager load related models
        $inventories = Inventory::with(['item', 'colour', 'size'])->paginate(10);
//        print($inventories);
        return view('manager.inventory.index', compact('inventories'));
    }

    public function create()
    {
        $items = Item::all();
        $colours = Colour::all();
        $sizes = Size::all();
        $suppliers = Supplier::all();
        $categories = Category::all();
        return view('manager.inventory.create', compact('items', 'colours', 'sizes','suppliers','categories'));
    }

    public function store(Request $request)
    {
        print_r($request->all());

        $request->validate([
            'itemid' => 'required',
            'colourIds' => 'required|array',
            'sizeIds' => 'required|array',
            'quantities' => 'required|array',
        ]);

        // Find the category and store of the item
        $item = Item::find($request->itemid);
        $category = $item->category;
        $storeId = $category->storeId;

        \DB::transaction(function () use ($request, $storeId) {
            for ($i = 0; $i < count($request->colourIds); $i++) {
                $inventory = Inventory::where('itemId', $request->itemid)
                    ->where('colourId', $request->colourIds[$i])
                    ->where('sizeId', $request->sizeIds[$i])
                    ->first();

                if ($inventory) {
                    $inventory->quantity += $request->quantities[$i];
                    $inventory->initialQuantity = $inventory->quantity;
                } else {
                    $inventory = Inventory::create([
                        'itemId' => $request->itemid,
                        'colourId' => $request->colourIds[$i],
                        'sizeId' => $request->sizeIds[$i],
                        'quantity' => $request->quantities[$i],
                        'initialQuantity' => $request->quantities[$i],
                        'storeId' => $storeId,
                    ]);
                }

                $inventory->save();
            }
        });

        $quantity = $request->quantities;

//        Fill supply table
        Supply::create([
            'itemId' => $request->itemid,
            'supplierId' => $request->supplierid,
            'quantity' => array_sum($quantity),
            'supplyDate' => Carbon::now(),
        ]);

        Session::flash('success', 'Inventory item added successfully.');

        return redirect()->route('inventory.index');
    }

    public function storeColor(Request $request)
    {
        if(Colour::where('colourName', $request->colorName)->exists()){
            return response()->json(['success' => false, 'message' => 'Color already exists']);
        }
        else{
            $colour = Colour::create([
                'colourName' => $request->colorName,
            ]);

            return response()->json(['success' => true, 'colour' => $colour]);
        }
    }

    public function storeSize(Request $request)
    {
        if (Size::where('sizeValue', $request->sizeValue)->exists()){
            return response()->json(['success' => false, 'message' => 'Size already exists']);
        }
        else{
            $size = Size::create([
                'sizeValue' => $request->sizeValue,
            ]);

            return response()->json(['success' => true, 'size' => $size]);
        }

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
