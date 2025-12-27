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
    Schema::create('user_dashboard_settings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('banner_path')->nullable(); // Đường dẫn ảnh
        $table->string('banner_title')->default('MỤC TIÊU & KỶ LUẬT'); // Tiêu đề
        $table->string('banner_quote')->nullable(); // Câu quote phụ (nếu cần)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_dashboard_settings');
    }
};
