<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Car;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample items
        Item::create([
            'code' => 'ITM001',
            'name' => 'Printer',
            'category' => 'Electronics',
            'quantity' => 5
        ]);

        Item::create([
            'code' => 'ITM002',
            'name' => 'Office Chair',
            'category' => 'Furniture',
            'quantity' => 10
        ]);

        Item::create([
            'code' => 'ITM003',
            'name' => 'Whiteboard',
            'category' => 'Office Supplies',
            'quantity' => 3
        ]);

        // Create sample cars
        Car::create([
            'code' => 'CAR001',
            'make_model' => 'Toyota Corolla',
            'year' => '2020',
            'status' => 'Available'
        ]);

        Car::create([
            'code' => 'CAR002',
            'make_model' => 'Honda Civic',
            'year' => '2019',
            'status' => 'Available'
        ]);

        Car::create([
            'code' => 'CAR003',
            'make_model' => 'Ford Ranger',
            'year' => '2021',
            'status' => 'Available'
        ]);
    }
}