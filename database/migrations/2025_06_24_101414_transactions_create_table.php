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

        $this->addForeignKey('Transaction', 'Card');
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }

    public function createTable(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->bigInteger('card_number');
            $table->integer('amount');
            $table->enum('type', ['incoming', 'outgoing']);
            $table->integer('card_id');
            $table->timestamps();
        });
    }
};
