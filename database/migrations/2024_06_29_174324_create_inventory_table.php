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
            $table->unsignedBigInteger('storeId')->nullable();
            $table->foreign('storeId')->references('storeId')->on('store')->onDelete('no action')->cascadeOnUpdate();
            $table->unsignedBigInteger('itemId')->nullable();
            $table->foreign('itemId')->references('itemId')->on('item')->onDelete('no action')->cascadeOnUpdate();
            $table->unsignedBigInteger('colourId')->nullable();
            $table->foreign('colourId')->references('colourId')->on('colour')->onDelete('no action')->cascadeOnUpdate();
            $table->unsignedBigInteger('sizeId')->nullable();
            $table->foreign('sizeId')->references('sizeId')->on('size')->onDelete('no action')->cascadeOnUpdate();
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
