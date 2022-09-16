<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemRefund extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_refund', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('item_id');
            $table->foreignId('refund_id');
            $table->foreignId('branch_id');
            $table->foreignId('sale_id');
            $table->foreignId('item_purchase_id');
            $table->double('sold_price');
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
        Schema::dropIfExists('item_refund');
    }
}
