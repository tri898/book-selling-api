<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('name');
            $table->string('address');
            $table->string('phone');
            $table->integer('total');
            $table->string('note')->nullable();
            $table->enum('status', ['Chờ xác nhận', 'Đã xác nhận', 'Đang giao','Giao thành công','Giao thất bại'])->default('Chờ xác nhận');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
