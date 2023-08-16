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
        Schema::create('product_changes', function (Blueprint $table) {
            $table->unsignedBigInteger("product_id");
            $table->foreign("product_id")
                ->on("products")
                ->references("id");

            $table->integer("count");
            $table->integer("type")->default(1)->comment("-1,1");
            $table->enum("status",[ "locked", "unlocked", "increase", "decrease"]);

            $table->string("reasonable_type")->nullable();
            $table->unsignedBigInteger("reasonable_id")->nullable();

            $table->unsignedBigInteger("reason_id");
            $table->foreign("reason_id")
                ->on("product_change_reasons")
                ->references("id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_changes');
    }
};
