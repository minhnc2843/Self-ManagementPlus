<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_dashboard_settings', function (Blueprint $table) {
            $table->boolean('show_banner_title')->default(true)->after('banner_position_y');
            $table->boolean('show_banner_quote')->default(true)->after('banner_position_y');
        });
    }

    public function down(): void
    {
        Schema::table('user_dashboard_settings', function (Blueprint $table) {
            $table->dropColumn([
                'show_banner_title',
                'show_banner_quote',
            ]);
        });
    }
};
