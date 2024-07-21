<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    public function index()
    {

        if(auth()->user()->hasRole('admin')){
            $items = Item::with('inventory')->paginate(10);
        }else{
            $managerId = Auth::guard('staff')->user()->staffId;

            $storeId = Store::where('managerId', $managerId)
                ->value('storeId');

            $items = Item::with('inventory')->whereHas('category', function ($query) use ($storeId) {
                $query->where('storeId', $storeId);
            })->paginate(10);
        }
        return view('manager.item.index', compact('items'));
    }

    public function create()
    {

        if(auth()->user()->hasRole('admin')){
            $categories = Category::all();
        }
        else{
            $managerId = Auth::guard('staff')->user()->staffId;
            $storeId = Store::where('managerId', $managerId)
                ->value('storeId');
            $categories = Category::where('storeId', $storeId)->get();

        }
        return view('manager.item.create', compact('categories'));

    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'itemName' => 'required|string|max:255',
                'categoryId' => 'required|exists:category,categoryId',
                'description' => 'nullable|string|max:500',
            ]);

            // Create a new Item instance and save it to the database
            $item = Item::create($validatedData);

            // Optional: Handle inventory adjustments if applicable
            // (e.g., initialize inventory with default quantities)

            if ($request->ajax()) {
                return response()->json(['success' => true, 'item' => $item]);
            }

            return redirect()->route('item.index')->with('success', 'Item added successfully.');

        } catch (ValidationException $e) {
            // Capture and log validation errors
            $errors = $e->validator->errors();
            Log::error('Validation errors while creating item: ', $errors->toArray());

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $errors->toArray()]);
            }

            // Redirect back with validation errors
            return redirect()->back()->withErrors($errors)->withInput();

        } catch (\Exception $e) {
            // Capture and log general errors
            Log::error('Error creating item: ' . $e->getMessage());

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'An error occurred while creating the item. Please try again.']);
            }

            // Redirect back with error message
            return redirect()->back()->with('error', 'An error occurred while creating the item. Please try again.');
        }
    }


    public function edit(Item $item)
    {
        if(auth()->user()->hasRole('admin')){
            $categories = Category::all();
        }
        else{
            $managerId = Auth::guard('staff')->user()->staffId;
            $storeId = Store::where('managerId', $managerId)
                ->value('storeId');
            $categories = Category::where('storeId', $storeId)->get();

        }
        return view('manager.item.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'itemName' => 'required|string|max:255',
                'categoryId' => 'required|exists:category,categoryId',
                'description' => 'nullable|string|max:500',
            ]);

            // Update the Item instance
            $item->update($validatedData);

            // Optional: Handle inventory adjustments if applicable
            // (e.g., update related inventory records if item details change)

            return redirect()->route('item.index')->with('success', 'Item updated successfully.');

        } catch (ValidationException $e) {
            // Capture and log validation errors
            $errors = $e->validator->errors();
            Log::error('Validation errors while updating item: ', $errors->toArray());

            // Redirect back with validation errors
            return redirect()->back()->withErrors($errors)->withInput();

        } catch (\Exception $e) {
            // Capture and log general errors
            Log::error('Error updating item: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->with('error', 'An error occurred while updating the item. Please try again.');
        }
    }


    public function destroy($id)
    {
        $item = Item::find($id);
        $item->delete();
        return response()->json(['success' => true]);
    }
}
