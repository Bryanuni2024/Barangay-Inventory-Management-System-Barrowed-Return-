<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BorrowedItemController; // Add this line

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Test route without middleware
Route::get('/test-dashboard', [App\Http\Controllers\InventoryController::class, 'dashboard'])->name('test.dashboard');

// Add these to your inventory routes
Route::prefix('inventory')->group(function () {
    // ... your existing routes ...
    
    Route::get('/api/borrowed-items/{id}', [BorrowedItemController::class, 'show']);
    Route::post('/api/borrowed-items/{id}/extend', [BorrowedItemController::class, 'extendItem']);
});

// Debug session route
Route::get('/debug-session', function() {
    return response()->json([
        'session_id' => session()->getId(),
        'user_logged_in' => session('user_logged_in'),
        'user_id' => session('user_id'),
        'username' => session('username'),
        'all_session' => session()->all()
    ]);
});

// Simple test login route
Route::post('/test-login', function(Request $request) {
    $username = $request->input('username');
    $password = $request->input('password');
    
    $user = App\Models\User::where('email', $username)
               ->orWhere('name', $username)
               ->first();
    
    if ($user && Hash::check($password, $user->password)) {
        session(['user_logged_in' => true, 'user_id' => $user->id, 'username' => $user->name]);
        return redirect()->route('inventory.dashboard')->with('success', 'Login successful!');
    }
    
    return redirect()->route('login')->with('error', 'Invalid credentials');
});

Route::prefix('inventory')->name('inventory.')->middleware('auth.custom')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\InventoryController::class, 'dashboard'])->name('dashboard');
    Route::get('/items', [App\Http\Controllers\InventoryController::class, 'items'])->name('items');
    Route::get('/cars', [App\Http\Controllers\InventoryController::class, 'cars'])->name('cars');
    Route::get('/borrowed-items', [App\Http\Controllers\InventoryController::class, 'borrowedItems'])->name('borrowed_items');
    Route::get('/borrowed-cars', [App\Http\Controllers\InventoryController::class, 'borrowedCars'])->name('borrowed_cars');
    Route::get('/reports', [App\Http\Controllers\InventoryController::class, 'reports'])->name('reports');

    Route::resource('api/items', App\Http\Controllers\ItemController::class)->only(['index','store','show','update','destroy']);
    Route::resource('api/cars', App\Http\Controllers\CarController::class)->only(['index','store','show','update','destroy']);
    Route::resource('api/borrowed-items', App\Http\Controllers\BorrowedItemController::class)->only(['index','store']);
    Route::resource('api/borrowed-cars', App\Http\Controllers\BorrowedCarController::class)->only(['index','store']);
    Route::post('api/borrowed-items/{id}/return', [App\Http\Controllers\BorrowedItemController::class, 'returnItem']);
    Route::post('api/borrowed-cars/{id}/return', [App\Http\Controllers\BorrowedCarController::class, 'returnCar']);
    Route::post('api/borrowed-cars/{id}/extend', [App\Http\Controllers\BorrowedCarController::class, 'extendCar']);
});