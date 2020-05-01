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
            $table->id();
            $table->string('destination');
            $table->string('title');
            $table->string('doc_number');
            $table->string('doc_date');
            $table->string('note');
            $table->longText('images');
            $table->integer('company_id');
            $table->integer('state_id');
            $table->integer('type_id');
            $table->integer('user_id');
            $table->integer('action_id');
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
        Schema::dropIfExists('books');
    }
}
