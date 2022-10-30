<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleRevenueCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_revenue_cashes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_revenue_id')->constrained();
            $table->foreignId('cash_denomination_id')->constrained();
            $table->string('pieces');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_revenue_cashes');
    }
}
