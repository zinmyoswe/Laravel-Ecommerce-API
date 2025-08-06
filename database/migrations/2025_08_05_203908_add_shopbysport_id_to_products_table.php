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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('shopbysport_id')->nullable()->after('subcategory_id');
            $table->foreign('shopbysport_id')
                ->references('id')
                ->on('shop_by_sports')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['shopbysport_id']);
            $table->dropColumn('shopbysport_id');
        });
    }
};
