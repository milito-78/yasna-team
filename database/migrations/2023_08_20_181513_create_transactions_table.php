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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")
                ->on("users")
                ->references("id");

            $table->string("uuid")->unique();
            $table->string("tracking_code")->unique()->nullable();
            $table->string("transactionable_type")->nullable();
            $table->unsignedBigInteger("transactionable_id")->nullable();

            $table->unsignedInteger("price");
            $table->unsignedBigInteger("status_id");
            $table->foreign("status_id")
                ->on("transaction_statuses")
                ->references("id");

            $table->unsignedBigInteger("payment_id");
            $table->foreign("payment_id")
                ->on("payment_gateways")
                ->references("id");

            $table->timestamp("received_at")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
