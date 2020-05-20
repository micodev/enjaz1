<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifies', function (Blueprint $table) {
            $table->id();
            $table->integer('book_id')->nullable();
            $table->integer('contract_id')->nullable();
            $table->boolean('seen')->default(false);
            $table->integer('role_id')->nullable();
            $table->integer('user_id');
            $table->boolean('type'); // false = archive , true = create
            $table->boolean('notify_type'); // false = send , true = recieve
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
        Schema::dropIfExists('notifies');
    }
}
