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
use Illuminate\Validation\ValidationException;


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
        try {
            if (auth()->user()->hasRole('staff')) {
                $request['staffId'] = auth()->user()->staffId;
            } elseif ((auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin')) && $request->staffId == null) {
                $request['staffId'] = auth()->user()->staffId;
            }

            // Log the request data for debugging
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
            \DB::transaction(function () use ($request) {
                // Create the inventory request
                $inventoryRequest = InventoryRequest::create([
                    'date' => Carbon::now(),
                    'status' => 'pending',
                    'staffId' => $request->staffId,
                    'storeId' => $request->storeId,
                ]);

                // Create each request detail and adjust inventory and item quantities
                foreach ($request->data as $detail) {
                    Log::info('Request detail', ['detail' => $detail]);

                    // Create request detail
                    RequestDetail::create([
                        'requestId' => $inventoryRequest->requestId,
                        'itemId' => $detail['itemId'],
                        'quantity' => $detail['quantity'],
                        'colourId' => $detail['colourId'],
                        'sizeId' => $detail['sizeId'],
                    ]);

                    // Adjust inventory quantities
                    $inventory = Inventory::where('itemId', $detail['itemId'])
                        ->where('colourId', $detail['colourId'])
                        ->where('sizeId', $detail['sizeId'])
                        ->first();

                    if ($inventory) {
                        $inventory->quantity -= $detail['quantity'];
                        $inventory->save();

                        // Adjust item quantities
                        $item = Item::find($detail['itemId']);
                        if ($item) {
                            $item->quantity -= $detail['quantity'];
                            $item->save();
                        }
                    } else {
                        // Handle case where inventory item is not found (optional)
                        Log::warning('Inventory item not found for store.', $detail);
                    }
                }

                // Dispatch the notification job
                SendRequestNotification::dispatch($inventoryRequest, $request->staffId, $request->storeId);
            });

            return response()->json(['success' => true, 'redirect_url' => route('requests.index')]);

        } catch (ValidationException $e) {
            // Capture and log validation errors
            $errors = $e->validator->errors();
            Log::error('Validation errors while creating request: ', $errors->toArray());

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $errors->toArray()]);
            }

            // Redirect back with validation errors
            return redirect()->back()->withErrors($errors)->withInput();

        } catch (\Exception $e) {
            // Capture and log general errors
            Log::error('Error creating request: ' . $e->getMessage());

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'An error occurred while creating the request. Please try again.']);
            }

            // Redirect back with error message
            return redirect()->back()->with('error', 'An error occurred while creating the request. Please try again.');
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

                    // Also update the item quantity in the items table
                    $item = Item::find($detail['itemId']);
                    if ($item) {
                        $item->quantity -= $detail['quantity'];
                        $item->save();
                    }

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
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'itemIds' => 'required|array',
                'quantities' => 'required|array',
                'colourIds' => 'required|array',
                'sizeIds' => 'required|array',
            ]);

            // Retrieve the InventoryRequest instance
            $inventoryRequest = InventoryRequest::findOrFail($inventoryRequest);

            // Use a database transaction to ensure data integrity
            \DB::transaction(function () use ($request, $inventoryRequest) {
                // Remove existing request details
                RequestDetail::where('requestId', $inventoryRequest->requestId)->delete();

                // Update inventory and item quantities
                foreach ($request->itemIds as $index => $itemId) {
                    $quantity = $request->quantities[$index];
                    $colourId = $request->colourIds[$index];
                    $sizeId = $request->sizeIds[$index];

                    // Create new request details
                    RequestDetail::create([
                        'requestId' => $inventoryRequest->requestId,
                        'itemId' => $itemId,
                        'quantity' => $quantity,
                        'colourId' => $colourId,
                        'sizeId' => $sizeId,
                    ]);

                    // Adjust inventory quantities
                    $inventory = Inventory::where('itemId', $itemId)
                        ->where('colourId', $colourId)
                        ->where('sizeId', $sizeId)
                        ->first();

                    if ($inventory) {
                        $inventory->quantity -= $quantity;
                        $inventory->save();

                        // Adjust item quantities
                        $item = Item::find($itemId);
                        if ($item) {
                            $item->quantity -= $quantity;
                            $item->save();
                        }
                    } else {
                        // Handle case where inventory item is not found (optional)
                        Log::warning('Inventory item not found for update.', compact('itemId', 'colourId', 'sizeId'));
                    }
                }
            });

            Session::flash('success', 'Request updated successfully.');
            return redirect()->route('requests.index');

        } catch (ValidationException $e) {
            // Capture and log validation errors
            $errors = $e->validator->errors();
            Log::error('Validation errors while updating request: ', $errors->toArray());

            // Redirect back with validation errors
            return redirect()->back()->withErrors($errors)->withInput();

        } catch (\Exception $e) {
            // Capture and log general errors
            Log::error('Error updating request: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->with('error', 'An error occurred while updating the request. Please try again.');
        }
    }

    public function destroy($id)
    {
        $inventoryRequest = InventoryRequest::findOrFail($id);
        $inventoryRequest->delete();

        return response()->json(['success' => true]);

    }
}
