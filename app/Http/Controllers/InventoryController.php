<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Car;
use App\Models\BorrowedItem;
use App\Models\BorrowedCar;

class InventoryController extends Controller
{
    public function dashboard()
    {
        // Get real data from database
        $totalItems = Item::count();
        $totalCars = Car::count();
        $borrowedItems = BorrowedItem::where('status', 'active')->count();
        $borrowedCars = BorrowedCar::where('status', 'active')->count();
        $overdueItems = BorrowedItem::where('status', 'overdue')->count();
        $overdueCars = BorrowedCar::where('status', 'overdue')->count();
        
        // Get recent activities
        $recentBorrowedItems = BorrowedItem::with('item')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentBorrowedCars = BorrowedCar::with('car')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('inventory.dashboard', compact(
            'totalItems', 
            'totalCars', 
            'borrowedItems', 
            'borrowedCars',
            'overdueItems',
            'overdueCars',
            'recentBorrowedItems',
            'recentBorrowedCars'
        ));
    }

    public function items()
    {
        return view('inventory.items');
    }

    public function cars()
    {
        return view('inventory.cars');
    }

    public function borrowedItems()
    {
        return view('inventory.borrowed_items');
    }

    public function borrowedCars()
    {
        return view('inventory.borrowed_cars');
    }

    public function reports()
    {
        // Get data for reports
        $items = Item::all();
        $cars = Car::all();
        $borrowedItems = BorrowedItem::with('item')->get();
        $borrowedCars = BorrowedCar::with('car')->get();
        
        return view('inventory.reports', compact('items', 'cars', 'borrowedItems', 'borrowedCars'));
    }
}


