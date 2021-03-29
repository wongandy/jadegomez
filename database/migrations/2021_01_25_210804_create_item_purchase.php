<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemPurchase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_purchase', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('item_id')->onDelete('cascade');
            $table->foreignId('purchase_id')->onDelete('cascade');
            $table->foreignId('branch_id')->onDelete('cascade');
            $table->double('cost_price')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('status')->default('available');
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
        Schema::dropIfExists('item_purchase');
    }
}
