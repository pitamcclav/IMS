<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;

class ManagerController extends Controller
{
    public function dashboard()
    {
        $inventoryItemsCount = Inventory::count();
        $pendingRequestsCount = Request::where('status', 'Pending')->count();
        $itemsCount = Item::count();

        $inventoryData = Inventory::select('itemId', 'quantity as quantity')->get()->toArray();

        return view('manager.dashboard', compact('inventoryItemsCount', 'pendingRequestsCount', 'inventoryData', 'itemsCount'));
    }
}
