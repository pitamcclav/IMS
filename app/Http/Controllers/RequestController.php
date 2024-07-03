<?php

namespace App\Http\Controllers;

use App\Models\Colour;
use App\Models\Inventory;
use App\Models\Request as InventoryRequest;
use App\Models\Item;
use App\Models\RequestDetail;
use App\Models\Size;
use App\Models\Staff;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        $requests = InventoryRequest::with(['staff'])->get();
        return view('manager.request.index', compact('requests'));
    }

    public function create()
    {
        $items = Item::all();
        $staffs = Staff::all();
        $colours = Colour::all();
        $sizes = Size::all();
        return view('manager.request.create', compact('items', 'staffs', 'colours', 'sizes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staffId' => 'required',
            'itemIds' => 'required|array',
            'quantities' => 'required|array',
            'colourIds' => 'required|array',
            'sizeIds' => 'required|array',
        ]);

        // Fetch the storeId based on the selected items
        $storeId = null;
        foreach ($request->itemIds as $itemId) {
            $item = Item::find($itemId);
            if ($item) {
                $category = Category::find($item->categoryId);
                if ($category) {
                    if ($storeId === null) {
                        $storeId = $category->storeId;
                    } elseif ($storeId !== $category->storeId) {
                        return redirect()->back()->withErrors(['error' => 'All items must belong to the same store.']);
                    }
                }
            }
        }

        if ($storeId === null) {
            return redirect()->back()->withErrors(['error' => 'Invalid items selected.']);
        }

        $data = [
            'date' => Carbon::now(),
            'status' => 'pending',
            'staffId' => $request->staffId,
            'storeId' => $storeId,
        ];

        $inventoryRequest = InventoryRequest::create($data);

        // Handle the request details
        foreach ($request->itemIds as $index => $itemId) {
            RequestDetail::create([
                'requestId' => $inventoryRequest->requestId,
                'itemId' => $itemId,
                'quantity' => $request->quantities[$index],
                'colourId' => $request->colourIds[$index],
                'sizeId' => $request->sizeIds[$index],
            ]);
        }

        return redirect()->route('requests.index')->with('success', 'Request added successfully.');
    }
    
    public function updateStatus(Request $request, $inventoryRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,ready,picked',
        ]);

        // Retrieve the InventoryRequest instance
        $inventoryRequest = InventoryRequest::findOrFail($inventoryRequest);

        // Check if the status is changing to "picked"
        if ($request->status == 'picked') {
            // Adjust inventory
            foreach ($inventoryRequest->requestDetails as $itemRequest) {
                $inventory = Inventory::where('itemId', $itemRequest->itemId)
                    ->where('colourId', $itemRequest->colourId)
                    ->where('sizeId', $itemRequest->sizeId)
                    ->first();

                if ($inventory) {
                    // Subtract the requested quantity from the inventory
                    $inventory->quantity -= $itemRequest->quantity;
                    $inventory->save();
                } else {
                    // Handle case where the inventory item is not found (optional)
                    return redirect()->route('requests.index')->with('error', 'Inventory item not found.');
                }
            }
        }

        // Update the request status
        $inventoryRequest->update(['status' => $request->status]);

        return redirect()->route('requests.index')->with('success', 'Request status updated successfully.');
    }

    public function edit(InventoryRequest $request)
    {
        $items = Item::all();
        $staffs = Staff::all();
        $colours = Colour::all();
        $sizes = Size::all();
        return view('manager.request.edit', compact('request', 'items', 'staffs', 'colours', 'sizes'));
    }

    public function update(Request $request, $inventoryRequest)
    {
        $request->validate([
            'itemIds' => 'required|array',
            'quantities' => 'required|array',
            'colourIds' => 'required|array',
            'sizeIds' => 'required|array',
        ]);

        // Retrieve the InventoryRequest instance
        $inventoryRequest = InventoryRequest::findOrFail($inventoryRequest);

        // Update the request details
        RequestDetail::where('requestId', $inventoryRequest->requestId)->delete();
        foreach ($request->itemIds as $index => $itemId) {
            RequestDetail::create([
                'requestId' => $inventoryRequest->requestId,
                'itemId' => $itemId,
                'quantity' => $request->quantities[$index],
                'colourId' => $request->colourIds[$index],
                'sizeId' => $request->sizeIds[$index],
            ]);
        }

        return redirect()->route('requests.index')->with('success', 'Request updated successfully.');
    }

    public function destroy(InventoryRequest $request)
    {
        $request->delete();
        return redirect()->route('requests.index')->with('success', 'Request deleted successfully.');
    }
}