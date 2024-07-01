<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
            $table->id('categoryId'); // Ensure categoryId is the primary key
            $table->string('categoryName');
            $table->boolean('isReturnable');
            $table->unsignedBigInteger('storeId');
            $table->foreign('storeId')->references('storeId')->on('store')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category');
    }
};
