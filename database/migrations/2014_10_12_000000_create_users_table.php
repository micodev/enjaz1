<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->integer('company_id');
            $table->integer('role_id');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        DB::table('users')->insert([
            [
                'name' => 'admin',
                'username' => 'admin',
                'password' => Hash::make('123'),
                'role_id' => 1,
                'company_id' => 1,
                'active' => true
            ],
            [
                'name' => 'super',
                'username' => 'super',
                'password' => Hash::make('123'),
                'role_id' => 2,
                'company_id' => 1,
                'active' => true
            ],
            [
                'name' => 'user',
                'username' => 'user',
                'password' => Hash::make('123'),
                'role_id' => 3,
                'company_id' => 1,
                'active' => true
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
        Schema::dropIfExists('users');
    }
}
