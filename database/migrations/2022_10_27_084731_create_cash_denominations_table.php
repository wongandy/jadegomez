<?php

use Database\Seeders\CashDenominationSeeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
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

        Artisan::call('db:seed', [
            '--class' => CashDenominationSeeder::class
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
