<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelPagseguroCreditCardTokens extends Migration
{
    public function up()
    {
        Schema::create('laravelpagseguro_credit_card_tokens', function (Blueprint $table) {
            $table->id();

            $table->string('number');
            $table->string('token');
            $table->unsignedBigInteger('ownable_id');
            $table->string('ownable_type');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laravelpagseguro_credit_card_tokens');
    }
}
