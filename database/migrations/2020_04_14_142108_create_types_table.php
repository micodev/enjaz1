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
            $table->id();
            $table->string('value');
            $table->string('table');
            $table->timestamps();
        });

        DB::table('types')->insert([
            [
                'value' => 'عقود مديريات',
                'table' => '0'
            ],
            [
                'value' => 'عقود شركات',
                'table' => '0'
            ],
            [
                'value' => 'سري \ شخصي',
                'table' => '1'
            ],
            [
                'value' => 'كتاب عام',
                'table' => '1'
            ],
            [
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
