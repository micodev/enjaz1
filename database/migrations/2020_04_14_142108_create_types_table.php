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
            $table->boolean('table'); // false = contract , true = book
            $table->timestamps();
        });

        DB::table('types')->insert([
            [
                'id' => 1,
                'value' => 'عقود مديريات',
                'table' => false
            ],
            [
                'id' => 2,
                'value' => 'عقود شراكات',
                'table' => false
            ],
            [
                'id' => 3,
                'value' => 'سري \ شخصي',
                'table' => true
            ],
            [
                'id' => 4,
                'value' => 'كتاب عام',
                'table' => true
            ],
            [
                'id' => 5,
                'value' => 'عروض خدمات',
                'table' => true
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
