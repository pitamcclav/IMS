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
        Schema::create('item_order_limit', function (Blueprint $table) {
            $table->id('limitId');
            $table->unsignedBigInteger('itemId');
            $table->foreign('itemId')->references('itemId')->on('item')->onDelete('cascade');
            $table->integer('orderLimit');
            $table->string('period');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_order_limit');
    }
};
