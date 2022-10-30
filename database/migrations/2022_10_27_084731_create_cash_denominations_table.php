<?php

use App\Models\CashDenomination;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashDenominationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_denominations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number');
            $table->timestamps();
        });

        CashDenomination::insert([
            [
                'name' => 'one thousand',
                'number' => '1000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'five hundred',
                'number' => '500',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'two hundred',
                'number' => '200',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'one hundred',
                'number' => '100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'fifty',
                'number' => '50',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'twenty',
                'number' => '20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ten',
                'number' => '10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'five',
                'number' => '5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'one',
                'number' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'twenty-five cents',
                'number' => '0.25',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ten cents',
                'number' => '0.10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_denominations');
    }
}
