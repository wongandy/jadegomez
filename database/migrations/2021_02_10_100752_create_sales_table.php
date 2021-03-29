<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('customer_id');
            $table->foreignId('branch_id');
            $table->foreignId('user_id');
            $table->integer('number')->nullable();
            $table->string('sale_number');
            $table->double('gross_total');
            $table->double('discount')->nullable();
            $table->double('net_total');
            $table->string('status')->default('for approval');
            $table->double('cash_tendered')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->date('end_of_day_at')->nullable();
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
        Schema::dropIfExists('sales');
    }
}
