<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RonasIT\Support\Traits\MigrationTrait;

return new class extends Migration
{
    use MigrationTrait;

    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table
                ->foreignId('reissued_id')
                ->nullable()
                ->references('id')
                ->on('cards')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
