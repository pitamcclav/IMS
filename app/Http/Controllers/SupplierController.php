<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use http\Env\Response;
use Illuminate\Http\Request;

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
        $request->validate([
            'supplierName' => 'required',
            'contactInfo' => 'required',
        ]);

        $supplier=Supplier::create($request->all());

        if($request->ajax()){
            return response()->json(['success'=>true,'supplier'=>$supplier]);
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier added successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('manager.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'supplierName' => 'required',
            'contactInfo' => 'required',
        ]);

        $supplier->update($request->all());

        return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier deleted successfully.');
    }
}
