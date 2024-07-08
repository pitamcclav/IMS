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
        Schema::create('request_detail', function (Blueprint $table) {
            $table->id('requestDetailId'); // Ensure requestDetailId is the primary key
            $table->unsignedBigInteger('requestId');
            $table->foreign('requestId')
                ->references('requestId')
                ->on('request')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('itemId')->nullable();
            $table->foreign('itemId')
                ->references('itemId')
                ->on('item')
                ->onDelete('no action')
                ->onUpdate('cascade');
            $table->integer('quantity');
            $table->unsignedBigInteger('colourId');
            $table->foreign('colourId')
                ->references('colourId')
                ->on('colour')
                ->onDelete('no action');
            $table->unsignedBigInteger('sizeId');
            $table->foreign('sizeId')
                ->references('sizeId')
                ->on('size')
                ->onDelete('no action');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_detail');
    }
};
