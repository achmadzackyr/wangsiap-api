<?php

use Illuminate\Database\Migrations\Migration;

class CreateTodayLoaderView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("
        CREATE VIEW today_loader_view AS
        (
            SELECT o.tanggal_pesan_string, o.order_status_id, c.nama, c.alamat,c.kodepos, c.hp, o.total_pcs, o.total_berat, p.nama deskripsi,
            o.total_harga, p.pecah_belah keterangan, o.payment_id, u.jne_id,
            u.nama nama_pengirim, u.alamat alamat_pengirim, u.kota kota_pengirim,
            u.kodepos kodepos_pengirim, u.provinsi provinsi_pengirim, u.hp hp_pengirim
            FROM customers c
            LEFT JOIN orders o ON c.id = o.customer_id
            LEFT JOIN ordered_products op ON o.id = op.order_id
            LEFT JOIN products p ON p.id = op.product_id
            LEFT JOIN users u ON u.id = o.user_id
            WHERE DATE(c.created_at) = CURDATE()
        )
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('DROP VIEW IF EXISTS today_loader_view');
    }
};
