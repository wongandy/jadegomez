<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDefectiveReplacementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_defective_replacement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id');
            $table->foreignId('defective_id');
            $table->foreignId('branch_id');
            $table->foreignId('item_purchase_id');
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
        Schema::dropIfExists('item_defective_replacement');
    }
}
