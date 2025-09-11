<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'borrower_name',
        'quantity_borrowed',
        'borrow_date',
        'due_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}