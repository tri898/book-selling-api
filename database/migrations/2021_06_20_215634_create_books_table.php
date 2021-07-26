<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name')->unique();
            $table->string('code');
            $table->longText('description');
            $table->integer('unit_price');
            $table->float('weight', 5, 2);
            $table->string('format');
            $table->year('release_date');
            $table->string('language');
            $table->string('size');
            $table->integer('num_pages');
            $table->string('slug');
            $table->string('translator')->nullable();
            $table->bigInteger('author_id')->unsigned();
            $table->bigInteger('publisher_id')->unsigned();
            $table->bigInteger('supplier_id')->unsigned();
            $table->timestamps();
            $table->foreign('author_id')->references('id')->on('authors');
            $table->foreign('publisher_id')->references('id')->on('publishers');
            $table->foreign('supplier_id')->references('id')->on('suppliers');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
