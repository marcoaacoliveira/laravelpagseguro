<?php
/*
 * Copyright (c) 2022. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLaravelpagseguroAuthorizations extends Migration
{
    public function up()
    {
        Schema::create('laravelpagseguro_authorizations', function (Blueprint $table) {
            $table->id();

            $table->string('code');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laravelpagseguro_authorizations');
    }
}
