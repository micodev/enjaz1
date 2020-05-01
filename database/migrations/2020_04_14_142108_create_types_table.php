<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->integer('id');
            $table->string('value');
            $table->string('table');
            $table->timestamps();
        });

        DB::table('types')->insert([
            [
                'id' => 1,
                'value' => 'عقود مديريات',
                'table' => '0'
            ],
            [
                'id' => 2,
                'value' => 'عقود شركات',
                'table' => '0'
            ],
            [
                'id' => 3,
                'value' => 'سري \ شخصي',
                'table' => '1'
            ],
            [
                'id' => 4,
                'value' => 'كتاب عام',
                'table' => '1'
            ],
            [
                'id' => 5,
                'value' => 'عروض خدمات',
                'table' => '1'
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
        Schema::dropIfExists('types');
    }
}
