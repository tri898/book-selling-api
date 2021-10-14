<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeAttrIntoGoodsReceivedNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_received_notes', function (Blueprint $table) {

            $table->bigInteger('supplier_id')->nullable()->unsigned()->change();
            $table->dropForeign(['supplier_id']);
            $table->foreign('supplier_id')->nullable()->references('id')->on('suppliers');

            $table->string('note')->after('total');
            $table->tinyInteger('formality')->after('supplier_id');
            $table->boolean('status')->default(true)->after('total');
            
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_received_notes', function (Blueprint $table) {
            //
        });
    }
}
