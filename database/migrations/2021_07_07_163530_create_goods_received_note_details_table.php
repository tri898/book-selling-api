<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsReceivedNoteDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_received_note_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('goods_received_note_id')->unsigned();
            $table->bigInteger('book_id')->unsigned();
            $table->integer('quantity');
            $table->integer('import_unit_price');
            $table->timestamps();
            $table->foreign('goods_received_note_id')->references('id')->on('goods_received_notes');
            $table->foreign('book_id')->references('id')->on('books');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_received_note_details');
    }
}
