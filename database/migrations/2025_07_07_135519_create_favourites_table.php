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
        Schema::create('favourites', function (Blueprint $table) {
            $table->id();

            // Authenticated user reference
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // ✅ Must match the actual primary key in `products` table
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                  ->references('productid') // ← Use actual primary key from `products` table
                  ->on('products')
                  ->onDelete('cascade');

            $table->string('size')->nullable(); // Optional size field
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favourites');
    }
};
