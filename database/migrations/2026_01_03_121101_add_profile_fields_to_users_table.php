<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('status')->nullable()->after('name'); // Trạng thái ngắn (vd: Đang bận, Sẵn sàng...)
        $table->text('profile_description')->nullable()->after('status'); // Mô tả ngắn/Tiểu sử
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'profile_description']);
        });
    }
};
