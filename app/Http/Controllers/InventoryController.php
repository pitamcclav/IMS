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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function index()
    {
        if(Auth::guard('staff')->user()->hasRole('admin')){
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
        if(Auth::guard('staff')->user()->hasRole('admin')){
            $items = Item::with('inventory')->get();
            $categories = Category::all();
        }else{
            $managerId = Auth::guard('staff')->user()->staffId;

            $storeId = Store::where('managerId', $managerId)
                ->value('storeId');

            $items = Item::with('inventory')->whereHas('category', function ($query) use ($storeId) {
                $query->where('storeId', $storeId);
            })->get();

            $categories = Category::where('storeId', $storeId)->get();
        }
        $colours = Colour::all();
        $sizes = Size::all();
        $suppliers = Supplier::all();
 
        return view('manager.inventory.create', compact('items', 'colours', 'sizes','suppliers','categories'));
    }

    public function store(Request $request)
    {
        Log::info('Request data: ' . json_encode($request->all()));
    
        try {
            $validatedData = $request->validate([
                'itemid' => 'required|exists:item,itemId',
                'supplierid' => 'required|exists:supplier,supplierId',
                'colourIds' => 'required|array',
                'colourIds.*' => 'exists:colour,colourId',
                'sizeIds' => 'required|array',
                'sizeIds.*' => 'exists:size,sizeId',
                'quantities' => 'required|array',
                'quantities.*' => 'integer|min:1',
                'uploadedFiles' => 'required|array',
                'uploadedFiles.*' => 'string'
            ]);
    
            $item = Item::findOrFail($validatedData['itemid']);
            $storeId = $item->category->storeId;
            $totalQuantity = array_sum($validatedData['quantities']);
            $uploadedFiles = $validatedData['uploadedFiles'];
    
            $result = DB::transaction(function () use ($request, $item, $storeId, $totalQuantity, $uploadedFiles) {
                $inventoryRecords = [];
                foreach ($request->colourIds as $index => $colourId) {
                    $existingInventory = Inventory::where([
                        'itemId' => $request->itemid,
                        'colourId' => $colourId,
                        'sizeId' => $request->sizeIds[$index],
                        'storeId' => $storeId,
                    ])->first();
    
                    if ($existingInventory) {
                        $existingInventory->quantity += $request->quantities[$index];
                        $existingInventory->initialQuantity += $request->quantities[$index];
                        $existingInventory->save();
                        $inventoryRecords[] = $existingInventory;
                    } else {
                        $inventory = Inventory::create([
                            'itemId' => $request->itemid,
                            'colourId' => $colourId,
                            'sizeId' => $request->sizeIds[$index],
                            'storeId' => $storeId,
                            'quantity' => $request->quantities[$index],
                            'initialQuantity' => $request->quantities[$index],
                        ]);
                        $inventoryRecords[] = $inventory;
                    }
                }
    
                $item->increment('initialQuantity', $totalQuantity);
                $item->increment('quantity', $totalQuantity);
    
                $processedFiles = [];
                foreach ($uploadedFiles as $filename) {
                    $tempPath = 'public/temp/inventory/' . $filename;
                    if (Storage::exists($tempPath)) {
                        $extension = pathinfo($filename, PATHINFO_EXTENSION);
                        $newFilename = Str::uuid() . '.' . $extension;
                        $newPath = 'public/inventory-images/' . $newFilename;
                        
                        Storage::move($tempPath, $newPath);
                        
                        $processedFiles[] = [
                            'id' => Str::uuid(),
                            'path' => str_replace('public/', '', $newPath),
                            'original_name' => $filename
                        ];
                    }
                }
    
                Supply::create([
                    'itemId' => $request->itemid,
                    'supplierId' => $request->supplierid,
                    'quantity' => $totalQuantity,
                    'supplyDate' => now(),
                    'delivery_notes' => json_encode($processedFiles),
                ]);
    
                Storage::deleteDirectory('public/temp/inventory');
    
                return [
                    'success' => true,
                    'message' => 'Inventory item added successfully',
                    'redirect' => route('inventory.index')
                ];
            });
    
            // Return JSON for AJAX requests
            if ($request->wantsJson()) {
                return response()->json($result);
            }
    
            // Fallback for non-AJAX (web) requests
            Session::flash('success', 'Inventory item added successfully.');
            return redirect()->route('inventory.index');
    
        } catch (ValidationException $e) {
            Log::error('Validation error while adding inventory: ' . json_encode($e->errors()));
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error adding inventory: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while adding the inventory item: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while adding the inventory item. Please try again.');
        }
    }
    public function storeColor(Request $request)
    {
        try {
            if (!$request->colorName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Color name is required'
                ], 422);
            }

            if (Colour::where('colourName', $request->colorName)->exists()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Color already exists'
                ], 422);
            }

            $colour = Colour::create([
                'colourName' => $request->colorName,
            ]);

            return response()->json([
                'success' => true,
                'colour' => $colour
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating color: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the color'
            ], 500);
        }
    }

    public function storeSize(Request $request)
    {
        try {
            if (!$request->sizeValue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Size value is required'
                ], 422);
            }

            if (Size::where('sizeValue', $request->sizeValue)->exists()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Size already exists'
                ], 422);
            }

            $size = Size::create([
                'sizeValue' => $request->sizeValue,
            ]);

            return response()->json([
                'success' => true,
                'size' => $size
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating size: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the size'
            ], 500);
        }
    }

    public function edit(Inventory $inventory)
    {
        if(Auth::guard('staff')->user()->hasRole('admin')){
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
            DB::transaction(function () use ($request, $inventory) {
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
        return response()->json(['success' => true]);
    }

    public function upload(Request $request)
{
    Log::info('Upload request received', [
        'user' => Auth::user(),
        'headers' => $request->headers->all(),
        'files' => $request->allFiles(),
        'input' => $request->all()
    ]);

    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized: Invalid token'
        ], 401);
    }

    try {
        if (!$request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'message' => 'No file provided'
            ], 400);
        }

        $file = $request->file('image');
        
        $validator = Validator::make(['file' => $file], [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:3072'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $filename = uniqid() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('temp/inventory', $filename, 'public');

        if (!$path) {
            throw new \Exception('Failed to store file on server');
        }

        // Ensure only JSON is returned
        return response()->json([
            'success' => true,
            'serverId' => $filename,
            'path' => Storage::url($path)
        ], 200);

    } catch (\Exception $e) {
        Log::error('File upload error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to upload file: ' . $e->getMessage()
        ], 500);
    }
}
}
