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
        Schema::create('posterslides', function (Blueprint $table) {
            $table->id('posterslideid');
            $table->string('posterslideimage');
            $table->string('posterslidename');
            $table->string('posterslidename2')->nullable();
            $table->string('buttonname')->nullable();
            $table->unsignedTinyInteger('part')->default(1);
            $table->boolean('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posterslides');
    }
};
