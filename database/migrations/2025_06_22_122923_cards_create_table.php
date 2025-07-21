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
        $this->createTable();

        $this->addForeignKey('Card', 'User');
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }

    public function createTable(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('name');
            $table->enum('status', ['active', 'freeze', 'broken', 'lost'])->default('active');
            $table->bigInteger('number')->unique();
            $table->integer('balance')->default(0);
            $table->timestamp('finished_at');
            $table->timestamps();
        });
    }
};
