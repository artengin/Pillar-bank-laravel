<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
        });

        DB::table('admins')->insert([
            'email' => 'admin@pillarbank.com',
            'password' => Hash::make('ASuDJKv4L'),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
