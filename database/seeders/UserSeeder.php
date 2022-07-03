<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'nama' => 'Asep Gumasep',
            'email' => 'asep@gmail.com',
            'hp' => '085223670378',
            'alamat' => 'Dusun kandanggajah dan sapi',
            'kecamatan' => 'Cijeungjing',
            'kota' => 'Ciamis',
            'provinsi' => 'Jawa Barat',
            'kodepos' => '46271',
            'gender' => false,
            'jne_id' => '123',
            'jne_id_cod' => '456',
            'password' => bcrypt('12345678'),
        ]);
    }
}
