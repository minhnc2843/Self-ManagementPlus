<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_reminders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('event_id');
            $table->dateTime('remind_at')->nullable();

            $table->boolean('is_sent')->default(false);

            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_reminders');
    }
};
