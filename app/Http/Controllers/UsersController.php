<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Request;
use App\Models\Staff;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{

    public function manager()
    {
        $managerId = Auth::guard('staff')->user()->staffId;

        $storeId = Store::where('managerId', $managerId)->value('storeId');

        $inventoryItemsCount = Inventory::where('storeId', $storeId)->count();

        $pendingRequestsCount = Request::where('storeId', $storeId)
            ->where('status', 'Pending')
            ->count();

        // Get all categories for the store
        $categories = Category::where('storeId', $storeId)->with('items')->get();

        // Initialize the items count and inventory data
        $itemsCount = 0;
        $inventoryData = [];

        foreach ($categories as $category) {
            // Count items in the category
            $category->itemsCount = $category->items->count();
            $itemsCount += $category->itemsCount;

            // Append category items to inventoryData
            foreach ($category->items as $item) {
                $inventoryData[] = [
                    'itemName' => $item->itemName,
                    'quantity' => $item->quantity
                ];
            }
        }

        return view('manager.dashboard', compact('inventoryItemsCount', 'pendingRequestsCount', 'inventoryData', 'itemsCount'));
    }

    public function admin()
    {
        // Query active sessions to get currently logged-in users
        $activeSessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->count();

        $usersCount = Staff::count();
        $storesCount = Store::count();
        $itemsCount = Item::count();

        // Fetch data for users pie chart
        $usersData = [
            'totalUsers' => $usersCount,
            'activeUsers' => $activeSessions,
            'inactiveUsers' => $usersCount - $activeSessions,
        ];

        // Fetch data for items pie chart by summing quantities from the inventory table
        $itemsData = Item::withSum('inventory as quantity', 'quantity')
            ->get(['id', 'name'])
            ->map(function($item) {
                return [
                    'itemName' => $item->itemName,
                    'quantity' => $item->quantity
                ];
            })
            ->toArray();

        return view('admin.dashboard', compact('usersCount', 'storesCount', 'itemsCount', 'activeSessions', 'usersData', 'itemsData'));
    }

    public function staff()
    {
        $staffId = Auth::guard('staff')->user()->staffId;

        // Fetch requests associated with the authenticated staff member
        $requestsCount = Request::where('staffId', $staffId)->count();
        $pendingRequestsCount = Request::where('staffId', $staffId)->where('status', 'Pending')->count();

        // Fetch the last 5 requests for the authenticated staff member
        $recentRequests = Request::where('staffId', $staffId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('staff.dashboard', compact('requestsCount', 'pendingRequestsCount', 'recentRequests'));
    }

}
