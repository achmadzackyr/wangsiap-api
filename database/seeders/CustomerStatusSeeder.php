<?php

namespace Database\Seeders;

use App\Models\CustomerStatus;
use Illuminate\Database\Seeder;

class CustomerStatusSeeder extends Seeder
{
    public function run()
    {
        CustomerStatus::create([
            'nama_status' => 'Pending',
        ]);
        CustomerStatus::create([
            'nama_status' => 'Approved',
        ]);
        CustomerStatus::create([
            'nama_status' => 'Rejected',
        ]);
        CustomerStatus::create([
            'nama_status' => 'Banned',
        ]);
    }
}
