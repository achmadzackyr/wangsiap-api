<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'nama' => 'admin',
        ]);
        Role::create([
            'nama' => 'user',
        ]);
        Role::create([
            'nama' => 'manager',
        ]);
    }
}
