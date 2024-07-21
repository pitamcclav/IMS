<?php

namespace App\Http\Controllers;

use App\Models\Colour;
use App\Models\Staff;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $staff = Staff::with('roles')->get();
        return view('admin.user', compact('staff', 'roles'));
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
            ]);

            // Create a new Staff instance and save it to the database
            $staff = new Staff();
            $staff->staffName = $validatedData['staffName'];
            $staff->email = $validatedData['email'];
            $staff->password = Hash::make($validatedData['password']);
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

        return response()->json(['success' => true]);

    }

    public function stores()
    {
        $stores = Store::with('manager')->get();
        $staff = Staff::all();
        return view('admin.stores', compact('stores', 'staff'));
    }

    public function addStore(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'storeName' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            ]);

            // Check if the store name already exists
            $store = Store::where('storeName', $request->storeName)->first();
            if ($store) {
                return response()->json(['error' => 'Store name already exists.'], 422);
            }

            // Create the store
            $store = Store::create([
                'storeName' => $validatedData['storeName'],
                'managerId' => $request->staffId ?? null,
                'location' => $validatedData['location'],
            ]);

            // Return a success response
            return response()->json(['success' => true, 'store' => $store]);

        } catch (ValidationException $e) {
            // Handle validation errors
            $errors = $e->validator->errors();
            Log::error('Validation errors while adding store: ', $errors->toArray());
            return response()->json(['error' => $errors->toArray()], 422);

        } catch (\Exception $e) {
            // Handle general exceptions
            Log::error('Error adding store: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while adding the store. Please try again.'], 500);
        }
    }

    public function editStore($id)
    {
        try {
            $store = Store::findOrFail($id);
            $staff = Staff::whereHas('roles', function ($query) {
                $query->where('name', 'manager');
            })->get();

            return view('admin.editStore', compact('store', 'staff'));

        } catch (\Exception $e) {
            Log::error('Error retrieving store for edit: ' . $e->getMessage());
            return redirect()->route('stores')->with('error', 'An error occurred while retrieving the store for editing.');
        }
    }

    public function updateStore(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'id' => 'required|exists:stores,id',
                'storeName' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            ]);

            // Find and update the store
            $store = Store::findOrFail($validatedData['id']);
            $store->update([
                'storeName' => $validatedData['storeName'],
                'managerId' => $request->staffId ?? null,
                'location' => $validatedData['location'],
            ]);

            return redirect()->route('stores')->with('success', 'Store updated successfully.');

        } catch (ValidationException $e) {
            // Handle validation errors
            $errors = $e->validator->errors();
            Log::error('Validation errors while updating store: ', $errors->toArray());
            return redirect()->back()->withErrors($errors)->withInput();

        } catch (\Exception $e) {
            // Handle general exceptions
            Log::error('Error updating store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the store. Please try again.');
        }
    }


    public function deleteStore($id)
    {
        $store = Store::findOrFail($id);
        $store->delete();

        return response()->json(['success' => true]);
    }

    public function assignRoles(Request $request)
    {
        Log::info($request);
        $staff = Staff::findOrFail($request->staff_id);
        $roles = $request->roles;
        Log::info($roles);
        Log::info($staff);

        DB::transaction(function () use ($staff, $roles) {
            $staff->assignRole($roles);
        });

        return response()->json(['status' => 'success']);
    }

    public function revokeRoles(Request $request, $staffId)
    {
        $staff = Staff::findOrFail($staffId);

        DB::transaction(function () use ($staff) {
            $staff->roles()->detach();
        });

        return redirect()->back()->with('status', 'Roles revoked successfully!');
    }



}
