<?php

namespace App\Http\Controllers;

use App\Models\Colour;
use App\Models\Staff;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function index()
    {
        $staff = Staff::all();
        return view('admin.user', compact('staff'));
    }

    public function create()
    {
        return view('admin.userCreate');
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'staffName' => 'required|string|max:255',
                'email' => 'required|email|unique:staff,email|max:255',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string|in:admin,staff,manager',
            ]);

            // Create a new Staff instance and save it to the database
            $staff = new Staff();
            $staff->staffName = $validatedData['staffName'];
            $staff->email = $validatedData['email'];
            $staff->password = Hash::make($validatedData['password']);
            $staff->role = $validatedData['role'];
            $staff->save();

            // Redirect to the users index with a success message
            return redirect()->route('users.index')->with('success', 'User created successfully.');

        } catch (ValidationException $e) {
            // Capture the validation errors
            $errors = $e->validator->errors();

            // Log the validation errors for debugging purposes
            \Log::error('Validation errors: ', $errors->toArray());

            // Redirect back with the validation errors
            return redirect()->back()->withErrors($errors)->withInput();
        } catch (\Exception $e) {
            // Log the error message and redirect back with an error message
            \Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the user. Please try again.');
        }
    }


    public function edit($id)
    {
        $user = Staff::find($id);
        return view('admin.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:staff,email,'.$id,
            'role' => 'required|string',
        ]);

        $user = Staff::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'sometimes|string|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = Staff::find($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function stores()
    {
        $stores = Store::with('staff')->get();
        $staff = Staff::all();
        return view('admin.stores', compact('stores', 'staff'));
    }

    public function addStore(Request $request)
    {


        $request->validate([
            'storeName' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);
        #check if store name already exists
        $store = Store::where('storeName', $request->storeName)->first();
        if ($store) {
            return response()->json(['error' => 'Store name already exists.'], 422);
        }

        $store = Store::create([
            'storeName' => $request->storeName,
            'staffId' => $request->staffId ?? null,
            'location' => $request->location,
        ]);

        /// Return a simple JSON response indicating success
        return response()->json(['success' => true]);
    }

    public function editStore($id)
    {
        $store = Store::find($id);
        $staff = Staff::all();
        return view('admin.editStore', compact('store', 'staff'));
    }

    public function updateStore(Request $request)
    {
        $request->validate([
            'storeName' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $store = Store::find($request->id);
        $store->storeName = $request->storeName;
        $store->staffId = $request->staffId ?? null;
        $store->location = $request->location;
        $store->save();

        return redirect()->route('stores')->with('success', 'Store updated successfully.');
    }

    public function deleteStore($id)
    {
        $store = Store::findOrFail($id);
        $store->delete();

        return response()->json(['success' => true]);
    }



}