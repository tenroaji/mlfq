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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('file_design')->nullable();
            $table->string('status')->nullable();
            $table->integer('size_s')->default(0);
            $table->integer('quantity_size_s')->default(0);
            $table->integer('size_m')->default(0);
            $table->integer('quantity_size_m')->default(0);
            $table->integer('size_l')->default(0);
            $table->integer('quantity_size_l')->default(0);
            $table->integer('size_xl')->default(0);
            $table->integer('quantity_size_xl')->default(0);
            $table->integer('size_2xl')->default(0);
            $table->integer('quantity_size_2xl')->default(0);
            $table->integer('size_3xl')->default(0);
            $table->integer('quantity_size_3xl')->default(0);
            $table->integer('size_4xl')->default(0);
            $table->integer('quantity_size_4xl')->default(0);
            $table->integer('total_product')->default(0);
            $table->integer('quantity_total_product')->default(0);
            $table->integer('total_time')->default(0);
            $table->bigInteger('sum_price')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
