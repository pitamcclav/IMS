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
        Schema::create('supply', function (Blueprint $table) {
            $table->id('supplyId'); // Ensure supplyId is the primary key
            $table->unsignedBigInteger('supplierId');
            $table->foreign('supplierId')
                ->references('supplierId')
                ->on('supplier')
                ->onDelete('no action');
            $table->unsignedBigInteger('itemId');
            $table->foreign('itemId')
                ->references('itemId')
                ->on('item')
                ->onDelete('no action');
            $table->integer('quantity');
            $table->date('supplyDate');
            $table->string('delivery_notes',1000)->nullable(); // Add a column for delivery notes
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply');
    }
};
