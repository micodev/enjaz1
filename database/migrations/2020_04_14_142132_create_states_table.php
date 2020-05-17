<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->integer('id');
            $table->string('value');
            $table->timestamps();
        });

        DB::table('states')->insert([
            [
                'id' => 1,
                'value' => 'مقبول'
            ],
            [
                'id' => 2,
                'value' => 'مرفوض'
            ],
            [
                'id' => 3,
                'value' => 'معلق'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
}
