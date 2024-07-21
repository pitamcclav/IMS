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
use Illuminate\Validation\ValidationException;

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
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'itemid' => 'required|exists:item,itemId',
                'supplierid' => 'required|exists:supplier,supplierId',
                'colourIds' => 'required|array',
                'sizeIds' => 'required|array',
                'quantities' => 'required|array',
                'kartik-input-711.*' => 'file|mimes:jpg,png,jpeg,pdf,docx|max:5000',
            ]);

            // Find the item and its category to determine the store
            $item = Item::findOrFail($request->itemid);
            $category = $item->category;
            $storeId = $category->storeId;

            \DB::transaction(function () use ($item, $request, $storeId) {
                for ($i = 0; $i < count($request->colourIds); $i++) {
                    $inventory = Inventory::where('itemId', $request->itemid)
                        ->where('colourId', $request->colourIds[$i])
                        ->where('sizeId', $request->sizeIds[$i])
                        ->first();

                    if ($inventory) {
                        // Update existing inventory record
                        $inventory->quantity += $request->quantities[$i];
                        $inventory->initialQuantity = $inventory->quantity;
                    } else {
                        // Create a new inventory record
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

                // Update item quantities
                $item->initialQuantity += array_sum($request->quantities);
                $item->quantity += array_sum($request->quantities);
                $item->save();
            });

            // Handle file uploads
            $uploadedFiles = [];
            if ($request->hasFile('kartik-input-711')) {
                foreach ($request->file('kartik-input-711') as $file) {
                    // Generate a unique file ID
                    $fileId = Str::uuid();
                    $extension = $file->getClientOriginalExtension();
                    $path = 'delivery-notes/' . $fileId . '.' . $extension;

                    // Store the file
                    $file->storeAs('public/delivery-notes', $fileId . '.' . $extension);

                    // Collect file info
                    $uploadedFiles[] = [
                        'id' => $fileId,
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName()
                    ];
                }
                Log::info('Uploaded files: ' . json_encode($uploadedFiles));
            }

            // Save the supply details
            $supply = Supply::create([
                'itemId' => $request->itemid,
                'supplierId' => $request->supplierid,
                'quantity' => array_sum($request->quantities),
                'supplyDate' => Carbon::now(),
                'delivery_notes' => json_encode($uploadedFiles), // Store file paths as JSON
            ]);

            // Flash success message and redirect
            Session::flash('success', 'Inventory item added successfully.');
            return redirect()->route('inventory.index');

        } catch (ValidationException $e) {
            // Handle validation errors
            $errors = $e->validator->errors();
            Log::error('Validation errors while adding inventory item: ', $errors->toArray());
            return redirect()->back()->withErrors($errors)->withInput();

        } catch (\Exception $e) {
            // Handle general exceptions
            Log::error('Error adding inventory item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while adding the inventory item. Please try again.');
        }
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
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'itemid' => 'required|exists:item,itemId',
                'colourid' => 'required|exists:colour,colourId',
                'sizeid' => 'required|exists:size,sizeId',
                'quantity' => 'required|integer|min:0',
                'initialQuantity' => 'required|integer|min:0',
            ]);

            // Use a database transaction to ensure data integrity
            \DB::transaction(function () use ($request, $inventory) {
                // Update the inventory record
                $inventory->update([
                    'itemId' => $request->itemid,
                    'colourId' => $request->colourid,
                    'sizeId' => $request->sizeid,
                    'quantity' => $request->quantity,
                    'initialQuantity' => $request->initialQuantity,
                ]);

                // Optionally, update the item quantity if required
                $item = Item::find($request->itemid);
                if ($item) {
                    // Adjust item quantities if needed
                    $totalQuantity = Inventory::where('itemId', $request->itemid)
                        ->sum('quantity');
                    $item->quantity = $totalQuantity;
                    $item->save();
                }
            });

            // Redirect with success message
            return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully.');

        } catch (ValidationException $e) {
            // Handle validation errors
            $errors = $e->validator->errors();
            Log::error('Validation errors while updating inventory: ', $errors->toArray());
            return redirect()->back()->withErrors($errors)->withInput();

        } catch (\Exception $e) {
            // Handle general exceptions
            Log::error('Error updating inventory item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the inventory item. Please try again.');
        }
    }


    public function destroy($id)
    {
        $inventory = Inventory::find($id);
        $inventory->delete();
        return response()->json(['success' => true]);}
}
