<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('supply.item')->get();
        return view('manager.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('manager.supplier.create');
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'supplierName' => 'required|string|max:255',
                'contactInfo' => 'required|string|max:255',
            ]);

            // Create a new supplier
            $supplier = Supplier::create($validatedData);

            // Always return JSON for API routes
            if (str_starts_with($request->path(), 'api/')) {
                return response()->json([
                    'success' => true,
                    'supplier' => $supplier
                ]);
            }

            // Return HTML response for web routes
            return redirect()->route('supplier.index')
                ->with('success', 'Supplier added successfully.');

        } catch (ValidationException $e) {
            if (str_starts_with($request->path(), 'api/')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Error creating supplier: ' . $e->getMessage());
            
            if (str_starts_with($request->path(), 'api/')) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while adding the supplier.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while adding the supplier. Please try again.');
        }
    }

    public function edit(Supplier $supplier)
    {
        return view('manager.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'supplierName' => 'required|string|max:255',
                'contactInfo' => 'required|string|max:255',
            ]);

            // Update the supplier
            $supplier->update($validatedData);

            // Redirect with success message
            return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully.');

        } catch (ValidationException $e) {
            // Capture and log validation errors
            $errors = $e->validator->errors();
            Log::error('Validation errors while updating supplier: ', $errors->toArray());

            // Redirect back with validation errors
            return redirect()->back()->withErrors($errors)->withInput();

        } catch (\Exception $e) {
            // Capture and log general errors
            Log::error('Error updating supplier: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->with('error', 'An error occurred while updating the supplier. Please try again.');
        }
    }

    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        $supplier->delete();
        return response()->json(['success' => true]);
    }
}
