<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "DROP PROCEDURE IF EXISTS `order_list`;
            CREATE PROCEDURE `order_list`(
                order_status_id_param INTEGER(1),
                no_penerima_param VARCHAR(25),
                date_from_param VARCHAR(50),
                date_to_param VARCHAR(50),
                limit_param INTEGER(100),
                offset_param INTEGER(200)
            )
            BEGIN
                    SELECT
                      `o`.`id` 			             AS `order_id`,
		                  `c`.`id` 			             AS `customer_id`,
		                  `op`.`id` 		             AS `ordered_product_id`,
                      `o`.`tanggal_pesan_string` AS `tanggal_pesan_string`,
                      `o`.`order_status_id`      AS `order_status_id`,
                      `os`.`order_status`        AS `order_status`,
                      `c`.`nama`                 AS `nama`,
                      `c`.`alamat`               AS `alamat`,
                      `c`.`kecamatan`            AS `kecamatan`,
                      `c`.`kota`                 AS `kota`,
                      `c`.`provinsi`             AS `provinsi`,
                      `c`.`kodepos`              AS `kodepos`,
                      `c`.`hp`                   AS `hp`,
                      `o`.`total_pcs`            AS `total_pcs`,
                      `o`.`total_berat`          AS `total_berat`,
                      `p`.`deskripsi`            AS `deskripsi`,
                      `o`.`ongkir`               AS `ongkir`,
                      `o`.`total_harga`          AS `total_harga`,
                      `p`.`pecah_belah`          AS `keterangan`,
                      `o`.`payment_id`           AS `payment_id`,
                      `u`.`jne_id`               AS `jne_id`,
                      `u`.`jne_id_cod`           AS `jne_id_cod`,
                      `u`.`nama`                 AS `nama_pengirim`,
                      `u`.`alamat`               AS `alamat_pengirim`,
                      `u`.`kota`                 AS `kota_pengirim`,
                      `u`.`kodepos`              AS `kodepos_pengirim`,
                      `u`.`provinsi`             AS `provinsi_pengirim`,
                      `u`.`hp`                   AS `hp_pengirim`,
                      `o`.`created_at`           AS `created_at`,
                      (SELECT COUNT(id) FROM orders WHERE order_status_id = order_status_id_param COLLATE utf8mb4_unicode_ci AND no_penerima = no_penerima_param COLLATE utf8mb4_unicode_ci
                         AND created_at >= date_from_param COLLATE utf8mb4_unicode_ci AND created_at <= date_to_param COLLATE utf8mb4_unicode_ci)           AS `total_row`
                    FROM (((((`customers` `c`
                           LEFT JOIN `orders` `o`
                         ON ((`c`.`id` = `o`.`customer_id`)))
                          LEFT JOIN `order_statuses` `os`
                        ON ((`o`.`order_status_id` = `os`.`id`)))
                         LEFT JOIN `ordered_products` `op`
                           ON ((`o`.`id` = `op`.`order_id`)))
                        LEFT JOIN `products` `p`
                          ON ((`p`.`id` = `op`.`product_id`)))
                       LEFT JOIN `users` `u`
                         ON ((`u`.`id` = `o`.`user_id`)))
                         WHERE o.order_status_id = order_status_id_param COLLATE utf8mb4_unicode_ci AND o.no_penerima = no_penerima_param COLLATE utf8mb4_unicode_ci
                         AND o.created_at >= date_from_param COLLATE utf8mb4_unicode_ci AND o.created_at <= date_to_param COLLATE utf8mb4_unicode_ci
                    ORDER BY `o`.`created_at` DESC LIMIT limit_param OFFSET offset_param;
                END;
            ";

        \DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
