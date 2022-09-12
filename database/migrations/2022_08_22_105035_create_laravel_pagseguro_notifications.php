<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelPagseguroNotifications extends Migration
{
    public function up()
    {
        Schema::create('laravelpagseguro_notifications', function (Blueprint $table) {
            $table->id();

            $table->string('notificationCode')->unique();
            $table->string('notificationType')->unique();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laravelpagseguro_notifications');
    }
}
