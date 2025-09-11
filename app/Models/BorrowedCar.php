<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowedCar extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'borrower_name',
        'borrow_date',
        'due_date',
        'status',
        'notes',
        'extensions_count' // Add this so mass assignment works
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}