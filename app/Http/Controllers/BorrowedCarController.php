<?php

namespace App\Http\Controllers;

use App\Models\BorrowedCar;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BorrowedCarController extends Controller
{
    public function index()
    {
        $borrowedCars = BorrowedCar::with('car')->orderBy('created_at', 'desc')->get();
        return response()->json($borrowedCars);
    }

    public function store(Request $request)
    {
        Log::info('BorrowedCarController@store request', $request->all());
        try {
            $validated = $request->validate([
                'car_id' => 'required|exists:cars,id',
                'borrower_name' => 'required|string|max:255',
                'borrow_date' => 'required|date',
                'due_date' => 'required|date|after:borrow_date',
                'notes' => 'nullable|string'
            ]);

            // Check if car is available
            $car = Car::findOrFail($validated['car_id']);
            if ($car->status !== 'Available') {
                Log::warning('Car not available', ['car_id' => $car->id, 'status' => $car->status]);
                return response()->json(['error' => 'Car is not available'], 400);
            }

            // Create borrowed car record
            $borrowedCar = BorrowedCar::create($validated);

            // Update car status
            $car->status = 'Borrowed';
            $car->save();

            Log::info('Borrowed car created', $borrowedCar->toArray());
            return response()->json($borrowedCar, 201);
        } catch (\Exception $e) {
            Log::error('Error borrowing car', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function returnCar(Request $request, $id)
    {
        $borrowedCar = BorrowedCar::findOrFail($id);
        
        // Return car to available status
        $car = $borrowedCar->car;
        $car->status = 'Available';
        $car->save();

        // Mark as returned
        $borrowedCar->status = 'returned';
        $borrowedCar->save();

        return response()->json(['message' => 'Car returned successfully']);
    }
}