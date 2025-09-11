<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Item::orderBy('id', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'nullable|string|max:255',
                'quantity' => 'required|integer|min:0',
            ]);
            
            // Generate automatic code
            $validated['code'] = $this->generateItemCode();
            
            Item::create($validated);
            return redirect()->route('inventory.items')->with('status', 'Item created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('inventory.items')->with('error', 'Error creating item: ' . $e->getMessage());
        }
    }

    /**
     * Generate automatic item code
     */
    private function generateItemCode()
    {
        $lastItem = Item::orderBy('id', 'desc')->first();
        
        if ($lastItem) {
            // Check if the code starts with 'ITM'
            if (strpos($lastItem->code, 'ITM') === 0) {
                $nextNumber = intval(substr($lastItem->code, 3)) + 1;
            } else {
                // Handle old format codes (just numbers)
                $nextNumber = intval($lastItem->code) + 1;
            }
        } else {
            $nextNumber = 1;
        }
        
        return 'ITM' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Item::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Item::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
        ]);
        $item->update($validated);
        return redirect()->route('inventory.items')->with('status', 'Item updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        return redirect()->route('inventory.items')->with('status', 'Item deleted');
    }
}
