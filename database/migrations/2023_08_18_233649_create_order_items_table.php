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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("order_id");
            $table->foreign("order_id")
                ->on("orders")
                ->references("id");
            $table->unsignedBigInteger("product_id");
            $table->foreign("product_id")
                ->on("products")
                ->references("id");
            $table->unsignedBigInteger("old_price")->nullable();
            $table->unsignedBigInteger("price");
            $table->unsignedInteger("count");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
