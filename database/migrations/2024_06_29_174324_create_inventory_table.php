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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id('inventoryId'); // Ensure inventoryId is the primary key
            $table->integer('quantity');
            $table->integer('initialQuantity');
            $table->unsignedBigInteger('storeId');
            $table->foreign('storeId')->references('storeId')->on('store')->onDelete('cascade');
            $table->unsignedBigInteger('itemId');
            $table->foreign('itemId')->references('itemId')->on('item')->onDelete('cascade');
            $table->unsignedBigInteger('colourId');
            $table->foreign('colourId')->references('colourId')->on('colour')->onDelete('cascade');
            $table->unsignedBigInteger('sizeId');
            $table->foreign('sizeId')->references('sizeId')->on('size')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
