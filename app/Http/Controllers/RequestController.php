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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use function Symfony\Component\VarDumper\Dumper\esc;

class RequestController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'staff') {
            $requests = InventoryRequest::where('staffId', auth()->user()->staffId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return view('manager.request.index', compact('requests'));
        } else {
            $requests = InventoryRequest::with(['staff'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return view('manager.request.index', compact('requests'));
        }
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
        if (auth()->user()->role == 'staff' || auth()->user()->role == 'admin') {
            $request['staffId'] = auth()->user()->staffId;
        }else if(auth()->user()->role == 'manager'&& $request->staffId==null){
            $request['staffId'] = auth()->user()->staffId;

        }

        // Log the request data for debugging
        Log::info('Request data', $request->all());

        // Validate the incoming request data
        $request->validate([
            'data' => 'required|array',
            'data.*.itemId' => 'required|exists:item,itemId',
            'data.*.quantity' => 'required|integer|min:1',
            'data.*.colourId' => 'required|exists:colour,colourId',
            'data.*.sizeId' => 'required|exists:size,sizeId',
            'staffId' => 'required|exists:staff,staffId',
        ]);

        // Initialize storeId to null
        $storeId = null;

        // Loop through each request detail to fetch storeId
        foreach ($request->data as $detail) {
            $item = Item::find($detail['itemId']);
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

        // Use a database transaction to ensure data integrity
        \DB::transaction(function () use ($request, $storeId) {
            // Create the inventory request
            $inventoryRequest = InventoryRequest::create([
                'date' => Carbon::now(),
                'status' => 'pending',
                'staffId' => $request->staffId,
                'storeId' => $storeId,
            ]);

            // Create each request detail
            foreach ($request->data as $detail) {
                Log::info('Request detail', $detail);
                RequestDetail::create([
                    'requestId' => $inventoryRequest->requestId,
                    'itemId' => $detail['itemId'],
                    'quantity' => $detail['quantity'],
                    'colourId' => $detail['colourId'],
                    'sizeId' => $detail['sizeId'],
                ]);
            }
        });

        return response()->json(['success' => true, 'redirect_url' => route('requests.index')]);
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
                    Session::flash('error', 'Inventory item not found.');
                    return redirect()->route('requests.index');
                }
            }
        }

        // Update the request status
        $inventoryRequest->update(['status' => $request->status]);

        Session::flash('success', 'Request status updated successfully.');

        return redirect()->route('requests.index');
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

        Session::flash('success', 'Request updated successfully.');
        return redirect()->route('requests.index');
    }

    public function destroy($id)
    {
        $inventoryRequest = InventoryRequest::findOrFail($id);
        $inventoryRequest->delete();

        return response()->json(['success' => true]);

    }
}
