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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class RequestController extends Controller
{
    public function index()
    {
        $user = Auth::guard('staff')->user(); // Retrieve the authenticated staff

        if ($user && $user->hasRole('staff')) {
            $requests = InventoryRequest::where('staffId', $user->staffId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($user && $user->hasRole('manager')) {
            $manager = Staff::where('staffId', $user->staffId)->first();
            $stores = Store::where('managerId', $manager->staffId)->get();
            $requests = InventoryRequest::whereIn('storeId', $stores->pluck('storeId'))
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $requests = InventoryRequest::orderBy('created_at', 'desc')->paginate(10);
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
        try {
            $colours = Inventory::where('itemId', $itemId)
                ->join('colour', 'inventory.colourId', '=', 'colour.colourId')
                ->select('colour.*')
                ->distinct()
                ->get();

            return response()->json([
                'success' => true,
                'colours' => $colours->map(function($colour) {
                    return [
                        'colourId' => strval($colour->colourId),
                        'colourName' => $colour->colourName
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching colours: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch colours'
            ], 500);
        }
    }

    public function fetchSizes($itemId, $colourId)
    {
        try {
            $sizes = Inventory::where('itemId', $itemId)
                ->where('colourId', $colourId)
                ->join('size', 'inventory.sizeId', '=', 'size.sizeId')
                ->select('size.*')
                ->distinct()
                ->get();

            return response()->json([
                'success' => true,
                'sizes' => $sizes->map(function($size) {
                    return [
                        'sizeId' => strval($size->sizeId),
                        'sizeValue' => $size->sizeValue
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching sizes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sizes'
            ], 500);
        }
    }

    public function fetchItems($storeId)
    {
        try {
            $items = Item::whereHas('category', function ($query) use ($storeId) {
                $query->where('storeId', $storeId);
            })->get();

            return response()->json($items);
        } catch (\Exception $e) {
            Log::error('Error fetching items: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch items'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Request data', $request->all());

            // Validate the incoming request data
            $validatedData = $request->validate([
                'data' => 'required|array',
                'data.*.itemId' => 'required|exists:item,itemId',
                'data.*.quantity' => 'required|integer|min:1',
                'data.*.colourId' => 'required|exists:colour,colourId',
                'data.*.sizeId' => 'required|exists:size,sizeId',
                'staffId' => 'required|exists:staff,staffId',
                'storeId' => 'required|exists:store,storeId',
            ]);

            // Use a database transaction to ensure data integrity
            $inventoryRequest = DB::transaction(function () use ($request) {
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
                
                return $inventoryRequest;
            });

            return response()->json([
                'success' => true, 
                'redirect_url' => route('requests.index')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in request creation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in request creation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }
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
        // Eager load the relationships we need
       $request->load(['requestDetails.item', 'requestDetails.colour', 'requestDetails.size']);


        
        $items = Item::all();
        $staffs = Staff::all();
        $colours = Colour::all();
        $sizes = Size::all();
        $stores = Store::all();

        
        return view('manager.request.edit', compact('request', 'items', 'staffs', 'colours', 'sizes', 'stores'));
    }

    public function update(Request $request, $inventoryRequest)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'details' => 'required|array',
                'details.*.itemId' => 'required|exists:item,itemId',
                'details.*.variants' => 'required|array',
                'details.*.variants.*.colourId' => 'required|exists:colour,colourId',
                'details.*.variants.*.sizeId' => 'required|exists:size,sizeId',
                'details.*.variants.*.quantity' => 'required|integer|min:1',
                'staffId' => 'required|exists:staff,staffId',
                'storeId' => 'required|exists:store,storeId',
            ]);

            // Retrieve the InventoryRequest instance
            $inventoryRequest = InventoryRequest::findOrFail($inventoryRequest);
            
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($inventoryRequest, $request) {
                // Update the request's store and staff
                $inventoryRequest->update([
                    'storeId' => $request->storeId,
                    'staffId' => $request->staffId
                ]);

                // Delete existing details
                RequestDetail::where('requestId', $inventoryRequest->requestId)->delete();

                // Create new request details
                foreach ($request->details as $detail) {
                    foreach ($detail['variants'] as $variant) {
                        RequestDetail::create([
                            'requestId' => $inventoryRequest->requestId,
                            'itemId' => $detail['itemId'],
                            'colourId' => $variant['colourId'],
                            'sizeId' => $variant['sizeId'],
                            'quantity' => $variant['quantity']
                        ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Request updated successfully',
                'redirect_url' => route('requests.index')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the request'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $inventoryRequest = InventoryRequest::findOrFail($id);
        $inventoryRequest->delete();

        return response()->json(['success' => true]);

    }
}
