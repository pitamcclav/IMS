<?php

namespace App\Http\Controllers;

use App\Jobs\SendRequestNotification;
use App\Models\Colour;
use App\Models\Inventory;
use App\Models\Request as InventoryRequest;
use App\Models\Item;
use App\Models\RequestDetail;
use App\Models\Size;
use App\Models\Staff;
use App\Models\Store;
use App\Notifications\LowStockNotification;
use App\Notifications\StatusChangedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class RequestController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('staff')) {
            $requests = InventoryRequest::where('staffId', auth()->user()->staffId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif (auth()->user()->hasRole('manager')) {
            $manager = Staff::where('staffId', auth()->user()->staffId)->first();
            $stores = Store::where('managerId', $manager->staffId)->get();
            $requests = InventoryRequest::whereIn('storeId', $stores->pluck('storeId'))
                ->orderBy('created_at', 'desc')
                ->paginate(10);

        }else{
            $requests = InventoryRequest::orderBy('created_at', 'desc')
                ->paginate(10);
        }
        return view('manager.request.index', compact('requests'));
    }


    public function create()
    {
        $stores = Store::all();
        $items = Item::all();
        $staffs = Staff::all();
        $colours = Colour::all();
        $sizes = Size::all();
        return view('manager.request.create', compact('items', 'staffs', 'colours', 'sizes', 'stores'));
    }

    public function fetchColours($itemId)
    {
        $inventory = Inventory::where('itemId', $itemId)->get();
        $colors = $inventory->pluck('colour')->unique();
        return response()->json($colors);
    }

    public function fetchSizes($itemId, $colourId)
    {
        $inventory = Inventory::where('itemId', $itemId)
            ->where('colourId', $colourId)
            ->get();
        $sizes = $inventory->pluck('size')->unique();
        return response()->json($sizes);
    }

    public function fetchItems($storeId)
    {
        $items = Item::whereHas('category', function ($query) use ($storeId) {
            $query->where('storeId', $storeId);
        })->get();
        Log::info('Items', $items->toArray());
        return response()->json($items);
    }

    public function store(Request $request)
    {
        if (auth()->user()->hasRole('staff')) {
            $request['staffId'] = auth()->user()->staffId;
        } elseif ((auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin')) && $request->staffId == null) {
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
            'storeId' => 'required|exists:store,storeId',
        ]);


        // Use a database transaction to ensure data integrity
        \DB::transaction(function () use ($request) {
            // Create the inventory request
            $inventoryRequest = InventoryRequest::create([
                'date' => Carbon::now(),
                'status' => 'pending',
                'staffId' => $request->staffId,
                'storeId' => $request->storeId,
            ]);

            // Create each request detail
            foreach ($request->data as $detail) {
                Log::info('Request detail', ['detail' => $detail]);
                RequestDetail::create([
                    'requestId' => $inventoryRequest->requestId,
                    'itemId' => $detail['itemId'],
                    'quantity' => $detail['quantity'],
                    'colourId' => $detail['colourId'],
                    'sizeId' => $detail['sizeId'],
                ]);
            }

            // Dispatch the notification job
            SendRequestNotification::dispatch($inventoryRequest, $request->staffId, $request->storeId);
        });



        return response()->json(['success' => true, 'redirect_url' => route('requests.index')]);
    }


    public function updateStatus(Request $request, $inventoryRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,ready,picked',
        ]);

        // Retrieve the InventoryRequest instance
        $inventoryRequest = InventoryRequest::with('requestDetails')->findOrFail($inventoryRequest);

        // Check if the status is changing to "picked"
        if ($request->status == 'picked') {
            // Get all itemIds, colourIds, and sizeIds in one query
            $itemDetails = $inventoryRequest->requestDetails->map(function ($itemRequest) {
                return [
                    'itemId' => $itemRequest->itemId,
                    'colourId' => $itemRequest->colourId,
                    'sizeId' => $itemRequest->sizeId,
                    'quantity' => $itemRequest->quantity,
                ];
            });

            // Fetch all matching inventory records
            $inventoryRecords = Inventory::whereIn('itemId', $itemDetails->pluck('itemId'))
                ->whereIn('colourId', $itemDetails->pluck('colourId'))
                ->whereIn('sizeId', $itemDetails->pluck('sizeId'))
                ->get();

            // Create a collection of inventory records indexed by a unique key
            $inventoryCollection = $inventoryRecords->keyBy(function ($inventory) {
                return $inventory->itemId . '-' . $inventory->colourId . '-' . $inventory->sizeId;
            });

            foreach ($itemDetails as $detail) {
                $key = $detail['itemId'] . '-' . $detail['colourId'] . '-' . $detail['sizeId'];
                $inventory = $inventoryCollection->get($key);

                if ($inventory) {
                    // Subtract the requested quantity from the inventory
                    $inventory->quantity -= $detail['quantity'];
                    $inventory->save();

                    // Check if the stock drops below 3/4 of the original quantity
                    $originalQuantity = $inventory->initialQuantity; // assuming you have this field
                    if ($inventory->quantity <= ($originalQuantity * 0.75)) {
                        // Notify the manager
                        $storeId = $inventoryRequest->storeId;
                        $store = Store::find($storeId);
                        $manager = Staff::where('staffId', $store->managerId)->first();
                        Log::info('Manager', $manager->toArray());
                        Log::info('Inventory', $inventory->toArray());
                        if ($manager) {
                            $manager->notify(new LowStockNotification($inventory));
                        }
                    }
                } else {
                    // Handle case where the inventory item is not found (optional)
                    Session::flash('error', 'Inventory item not found.');
                    return redirect()->route('requests.index');
                }
            }
        }

        // Update the request status
        $inventoryRequest->update(['status' => $request->status]);

        // Notify the staff about the status change
        $staff = Staff::where('staffId', $inventoryRequest->staffId)->first();
        if ($staff) {
            $staff->notify(new StatusChangedNotification($inventoryRequest));
        }

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
