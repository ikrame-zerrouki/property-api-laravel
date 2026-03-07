<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; 

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role column after password
            $table->string('role')->default('visiteur')->after('password');
            // Expected values: admin, agent, visiteur
        });


        DB::table('users')->insert([
            'name' => 'مدير النظام',
            'email' => 'admin@system.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ✅ حذف المستخدم الافتراضي فقط
        DB::table('users')->where('email', 'admin@system.com')->delete();

        Schema::table('users', function (Blueprint $table) {
            // Drop role column if rollback
            $table->dropColumn('role');
        });
    }
};
