<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subscription::create([
            'nama' => 'Trial',
            'jumlah_hari' => 7,
            'harga' => 0,
        ]);
        Subscription::create([
            'nama' => '1 Bulan',
            'jumlah_hari' => 30,
            'harga' => 150000,
        ]);
        Subscription::create([
            'nama' => '6 Bulan',
            'jumlah_hari' => 180,
            'harga' => 800000,
        ]);
        Subscription::create([
            'nama' => '1 Tahun',
            'jumlah_hari' => 365,
            'harga' => 1500000,
        ]);
        Subscription::create([
            'nama' => 'Tester',
            'jumlah_hari' => 9999,
            'harga' => 0,
        ]);

    }
}
