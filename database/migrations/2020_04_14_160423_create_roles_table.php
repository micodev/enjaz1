<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->integer('id');
            $table->string('value');
            $table->timestamps();
        });

        DB::table('roles')->insert([
            [
                'id' => 1,
                'value' => 'مدير'
            ],
            [
                'id' => 2,
                'value' => 'مشرف'
            ],
            [
                'id' => 3,
                'value' => 'مدير مكتب'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
