<?php

namespace Database\Seeders;

use App\Models\Reply;
use Illuminate\Database\Seeder;

class ReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $konfirmasiPemesanan = 'Konfirmasi Data Pemesanan \
\
Nama: ((NAMA)) \
Alamat: ((ALAMAT)) \
Kodepos: ((KODEPOS)) \
No.Hp: ((HP)) \
COD: ((COD)) \
\
Kodepos ((KODEPOS)) masuk ke Kabupaten / Kota ((KABUPATEN)) Kecamatan ((KECAMATAN)) \
\
Pesanan: *((PRODUK))* \
Harga: ((HARGAPRODUK)) \
Jumlah Pesan: ((JUMLAHPRODUK)) \
\
Ongkir: ((ONGKIR)) \
Total Harga: *((TOTALHARGA))* \
\
Apakah sudah sesuai? \
(Lakukan konfirmasi maksimal *((TANGGALKADALUARSA)))* \
\
*1* Ya, Pesanan Saya Sudah Sesuai \
*2* Tidak, Batalkan Pesanan Saya';

        Reply::create([
            'hp' => '1234567890987654321',
            'keyword' => 'default-konfirmasi-pemesanan',
            'type' => 'text',
            'reply' => stripslashes(json_encode(["text" => $konfirmasiPemesanan])),
        ]);

        Reply::create([
            'hp' => '1234567890987654321',
            'keyword' => 'default-pesanan-diterima',
            'type' => 'text',
            'reply' => stripslashes(json_encode(["text" => 'Terima kasih! Pesanan sudah masuk dan akan segera diproses'])),
        ]);

        Reply::create([
            'hp' => '1234567890987654321',
            'keyword' => 'default-pesanan-dibatalkan',
            'type' => 'text',
            'reply' => stripslashes(json_encode(["text" => 'Pesanan berhasil dibatalkan'])),
        ]);

        Reply::create([
            'hp' => '1234567890987654321',
            'keyword' => 'default-pesanan-kadaluarsa',
            'type' => 'text',
            'reply' => stripslashes(json_encode(["text" => 'Batas konfirmasi sudah terlewati (1 jam). Silahkan buat pesanan lagi!'])),
        ]);
    }
}
