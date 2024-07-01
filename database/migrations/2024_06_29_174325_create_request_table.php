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
        Schema::create('request', function (Blueprint $table) {
            $table->id('requestId'); // Ensure requestId is the primary key
            $table->timestamp('date');
            $table->string('status');
            $table->unsignedBigInteger('storeId');
            $table->foreign('storeId')->references('storeId')->on('store')->onDelete('cascade');
            $table->unsignedBigInteger('staffId');
            $table->foreign('staffId')->references('staffId')->on('staff')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request');
    }
};
