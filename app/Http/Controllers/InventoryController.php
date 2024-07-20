<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Colour;
use App\Models\Size;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Supply;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    public function index()
    {
        if(auth()->user()->hasRole('admin')){
            $inventories = Inventory::with(['item', 'colour', 'size', 'store'])
                ->paginate(10);

            return view('manager.inventory.index', compact('inventories'));
        }else{
            $managerId = Auth::guard('staff')->user()->staffId;

            $storeId = Store::where('managerId', $managerId)
                ->value('storeId');
            // Eager load related models
            $inventories = Inventory::with(['item', 'colour', 'size', 'store'])
                ->where('storeId', $storeId)
                ->paginate(10);

            return view('manager.inventory.index', compact('inventories'));
        }

    }

    public function create()
    {
        if(auth()->user()->hasRole('admin')){
            $items = Item::with('inventory')->get();
        }else{
            $managerId = Auth::guard('staff')->user()->staffId;

            $storeId = Store::where('managerId', $managerId)
                ->value('storeId');

            $items = Item::with('inventory')->whereHas('category', function ($query) use ($storeId) {
                $query->where('storeId', $storeId);
            })->get();
        }
        $colours = Colour::all();
        $sizes = Size::all();
        $suppliers = Supplier::all();
        $categories = Category::all();
        return view('manager.inventory.create', compact('items', 'colours', 'sizes','suppliers','categories'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'itemid' => 'required',
            'supplierid' => 'required',
            'colourIds' => 'required|array',
            'sizeIds' => 'required|array',
            'quantities' => 'required|array',
            'kartik-input-711.*' => 'required|file|mimes:jpg,png,jpeg,pdf,docx|max:5000',
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

        // Handle file uploads
        $uploadedFiles = [];
        if ($request->hasFile('kartik-input-711')) {
            $files = $request->file('kartik-input-711');
            foreach ($files as $file) {
                // Generate a unique file ID
                $fileId = Str::uuid();

                // Determine the file extension
                $extension = $file->getClientOriginalExtension();

                // Define the path with file ID and extension
                $path = 'delivery-notes/' . $fileId . '.' . $extension;

                // Store the file with the generated file ID
                $file->storeAs('public/delivery-notes', $fileId . '.' . $extension);

                // Store the path and ID for reference
                $uploadedFiles[] = [
                    'id' => $fileId,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ];
            }
            Log::info('Uploaded files: ' . json_encode($uploadedFiles));

        }

        // Save file paths to the supply table
        $supply = Supply::create([
            'itemId' => $request->itemid,
            'supplierId' => $request->supplierid,
            'quantity' => array_sum($request->quantities),
            'supplyDate' => Carbon::now(),
            'delivery_notes' => json_encode($uploadedFiles), // Save the uploaded file paths as JSON
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
        if(auth()->user()->hasRole('admin')){
            $items = Item::with('inventory')->get();
        }else{
            $managerId = Auth::guard('staff')->user()->staffId;

            $storeId = Store::where('managerId', $managerId)
                ->value('storeId');

            $items = Item::with('inventory')->whereHas('category', function ($query) use ($storeId) {
                $query->where('storeId', $storeId);
            })->get();
        }
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

    public function destroy($id)
    {
        $inventory = Inventory::find($id);
        $inventory->delete();
        return response()->json(['success' => true]);}
}
