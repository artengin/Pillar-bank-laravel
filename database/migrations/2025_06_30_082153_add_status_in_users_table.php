<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['approve', 'reject'])->nullable();
            $table->dropUnique('users_ssn_unique');
        });

        DB::table('users')->update(['status' => 'approve']);

        DB::statement('alter table users alter column status set not null');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->string('ssn')->unique()->change();
        });
    }
};
