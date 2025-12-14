<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('event_type')->nullable();

            $table->string('location')->nullable();
            $table->unsignedBigInteger('created_by');

            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();

            $table->string('repeat_rule')->nullable(); // none, weekly, monthly, yearly, custom
            $table->json('repeat_meta')->nullable();   // lưu thêm ví dụ: { "weekday": [3,4] }

            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->boolean('is_important')->default(false);

            $table->enum('status', [
                'upcoming',
                'confirmed',
                'attended',
                'declined',
                'missed'
            ])->default('upcoming');

            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
