<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('sku');
            $table->string('nama');
            $table->string('deskripsi');
            $table->decimal('harga', $precision = 10, $scale = 2);
            $table->decimal('berat', $precision = 8, $scale = 2);
            $table->decimal('lebar', $precision = 6, $scale = 2);
            $table->decimal('tinggi', $precision = 6, $scale = 2);
            $table->decimal('panjang', $precision = 6, $scale = 2);
            $table->string('dibuat_pada_string');
            $table->boolean('pecah_belah');
            $table->boolean('aktif');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
