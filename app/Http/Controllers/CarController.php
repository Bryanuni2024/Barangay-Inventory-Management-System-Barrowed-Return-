<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Car::orderBy('id', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'make_model' => 'required|string|max:255',
            'year' => 'nullable|string|max:50',
            'status' => 'required|string|max:50',
        ]);
        
        // Generate automatic code
        $validated['code'] = $this->generateCarCode();
        
        Car::create($validated);
        return redirect()->route('inventory.cars')->with('status', 'Car created');
    }

    /**
     * Generate automatic car code
     */
    private function generateCarCode()
    {
        $lastCar = Car::orderBy('id', 'desc')->first();
        
        if ($lastCar) {
            // Check if the code starts with 'CAR'
            if (strpos($lastCar->code, 'CAR') === 0) {
                $nextNumber = intval(substr($lastCar->code, 3)) + 1;
            } else {
                // Handle old format codes (just numbers)
                $nextNumber = intval($lastCar->code) + 1;
            }
        } else {
            $nextNumber = 1;
        }
        
        return 'CAR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Car::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $car = Car::findOrFail($id);
        $validated = $request->validate([
            'make_model' => 'required|string|max:255',
            'year' => 'nullable|string|max:50',
            'status' => 'required|string|max:50',
        ]);
        $car->update($validated);
        return redirect()->route('inventory.cars')->with('status', 'Car updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $car = Car::findOrFail($id);
        $car->delete();
        return redirect()->route('inventory.cars')->with('status', 'Car deleted');
    }
}
