<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number');
            $table->string('alamat');
            $table->boolean('priority');
            $table->integer('time');
            $table->bigInteger('down_payment')->default(0);
            $table->integer('sum_price')->default(0);
            $table->integer('total_product')->default(0);
            $table->string('status')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamp('antrian')->nullable();
            $table->boolean('finish')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
