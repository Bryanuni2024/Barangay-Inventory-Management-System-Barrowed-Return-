<?php

namespace App\Http\Controllers;

use App\Models\BorrowedItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowedItemController extends Controller
{
    public function index()
    {
        $borrowedItems = BorrowedItem::with('item')->orderBy('created_at', 'desc')->get();
        return response()->json($borrowedItems);
    }

    public function show($id)
    {
        $borrowedItem = BorrowedItem::with('item')->findOrFail($id);
        return response()->json($borrowedItem);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'borrower_name' => 'required|string|max:255',
            'quantity_borrowed' => 'required|integer|min:1',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after:borrow_date',
            'notes' => 'nullable|string'
        ]);

        // Check if item has enough quantity
        $item = Item::findOrFail($validated['item_id']);
        if ($item->quantity < $validated['quantity_borrowed']) {
            return response()->json(['error' => 'Not enough items available'], 400);
        }

        // Create borrowed item record
        $borrowedItem = BorrowedItem::create($validated);

        // Update item quantity
        $item->quantity -= $validated['quantity_borrowed'];
        $item->save();

        return response()->json($borrowedItem, 201);
    }

    public function returnItem(Request $request, $id)
    {
        $borrowedItem = BorrowedItem::findOrFail($id);
        
        // Return quantity to item
        $item = $borrowedItem->item;
        $item->quantity += $borrowedItem->quantity_borrowed;
        $item->save();

        // Mark as returned
        $borrowedItem->status = 'returned';
        $borrowedItem->save();

        return response()->json(['message' => 'Item returned successfully']);
    }

    public function extendItem(Request $request, $id)
    {
        $borrowedItem = BorrowedItem::findOrFail($id);
        
        // Validate the request
        $validated = $request->validate([
            'new_due_date' => 'required|date|after:'.$borrowedItem->due_date
        ]);
        
        // Check if item can be extended (not returned)
        if ($borrowedItem->status === 'returned') {
            return response()->json(['error' => 'Returned items cannot be extended'], 400);
        }
        
        // Optional: Add a limit on how many times an item can be extended
        $maxExtensions = 3; // You can adjust this value
        if ($borrowedItem->extensions_count >= $maxExtensions) {
            return response()->json(['error' => 'This item has reached the maximum number of extensions'], 400);
        }
        
        // Set new due date from request
        $newDueDate = Carbon::parse($validated['new_due_date']);
        
        // Update the borrowed item
        $borrowedItem->due_date = $newDueDate;
        $borrowedItem->extensions_count = $borrowedItem->extensions_count + 1;
        
        // Update status if it was overdue
        if ($borrowedItem->status === 'overdue') {
            $borrowedItem->status = 'active';
        }
        
        $borrowedItem->save();
        
        return response()->json([
            'message' => 'Borrow period extended successfully',
            'new_due_date' => $borrowedItem->due_date
        ]);
    }
}