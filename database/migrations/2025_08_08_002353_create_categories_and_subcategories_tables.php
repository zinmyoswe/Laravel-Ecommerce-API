<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('categoryid');
            $table->string('categoryname')->unique();
            $table->timestamps();
        });

        Schema::create('subcategories', function (Blueprint $table) {
            $table->id('subcategoryid');
            $table->string('subcategoryname');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('categoryid')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
    }
};
