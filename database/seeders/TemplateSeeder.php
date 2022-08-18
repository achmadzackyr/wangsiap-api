<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Template::create([
            'nama' => 'Konfirmasi Pemesanan',
        ]);
        Template::create([
            'nama' => 'Konfirmasi Pesanan Diterima',
        ]);
        Template::create([
            'nama' => 'Konfirmasi Pesanan Dibatalkan',
        ]);
        Template::create([
            'nama' => 'Error Format Chat',
        ]);
        Template::create([
            'nama' => 'Error Input Pesanan',
        ]);
        Template::create([
            'nama' => 'Error Cek Tarif JNE',
        ]);
        Template::create([
            'nama' => 'Error Kodepos',
        ]);
        Template::create([
            'nama' => 'Error Cek SKU',
        ]);
        Template::create([
            'nama' => 'Error Cek Penjual',
        ]);
        Template::create([
            'nama' => 'Error Konfirmasi Pesanan',
        ]);
        Template::create([
            'nama' => 'Error Pesanan Kadaluarsa',
        ]);
        Template::create([
            'nama' => 'Error Cek Pesanan',
        ]);
    }
}
