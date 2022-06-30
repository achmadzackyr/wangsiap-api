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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_status_id');
            $table->string('nama');
            $table->string('alamat');
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('kodepos');
            $table->string('hp');
            $table->string('order_date_string');
            $table->foreign('customer_status_id')->references('id')->on('customer_statuses')->onDelete('cascade');
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
        Schema::dropIfExists('customers');
    }
};
