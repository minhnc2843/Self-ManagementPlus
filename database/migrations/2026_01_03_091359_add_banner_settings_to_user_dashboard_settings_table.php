<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_dashboard_settings', function (Blueprint $table) {
            $table->integer('banner_height')
                ->nullable()
                ->default(280)
                ->comment('Chiều cao banner (px)');

            $table->integer('banner_position_y')
                ->nullable()
                ->default(50)
                ->comment('Vị trí banner theo trục Y (%)');
        });
    }

    public function down(): void
    {
        Schema::table('user_dashboard_settings', function (Blueprint $table) {
            $table->dropColumn([
                'banner_height',
                'banner_position_y',
            ]);
        });
    }
};
