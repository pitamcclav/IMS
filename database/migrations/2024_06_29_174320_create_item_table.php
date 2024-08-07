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
        Schema::create('item', function (Blueprint $table) {
            $table->id('itemId'); // Ensure itemId is the primary key
            $table->string('itemName');
            $table->unsignedBigInteger('categoryId')->nullable();
            $table->foreign('categoryId')->references('categoryId')
                ->on('category')
                ->onDelete('no action')
                ->cascadeOnUpdate();
            $table->integer('quantity')->nullable();
            $table->integer('initialQuantity')->nullable();
            $table->text('description');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item');
    }
};
