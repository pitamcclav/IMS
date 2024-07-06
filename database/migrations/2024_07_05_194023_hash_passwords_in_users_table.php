<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        $staffMembers = DB::table('staff')->get();

        // Hash each staff member's password using Bcrypt
        foreach ($staffMembers as $staffMember) {
            DB::table('staff')
                ->where('staffId', $staffMember->staffId)
                ->update(['password' => Hash::make($staffMember->password)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
