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
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")
                ->on("users")
                ->references("id");

            $table->bigInteger("total_price");
            $table->bigInteger("pay_price");
            $table->bigInteger("discount_price")->nullable();

            $table->unsignedBigInteger('status_id');
            $table->foreign("status_id")
                ->on("order_statuses")
                ->references("id");

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
