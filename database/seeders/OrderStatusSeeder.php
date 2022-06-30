<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderStatus::create([
            'order_status' => 'Pending',
        ]);
        OrderStatus::create([
            'order_status' => 'Diterima',
        ]);
        OrderStatus::create([
            'order_status' => 'Diproses',
        ]);
        OrderStatus::create([
            'order_status' => 'Selesai',
        ]);
        OrderStatus::create([
            'order_status' => 'Batal',
        ]);
    }
}
