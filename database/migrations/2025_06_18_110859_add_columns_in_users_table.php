<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'first_name');
            $table->string('last_name');
            $table->string('phone')->unique();
            $table->string('ssn')->unique();
            $table->dropColumn('remember_token');
            $table->dropColumn('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('first_name', 'name');
            $table->dropColumn(['last_name', 'phone', 'ssn']);
        });
    }
};
