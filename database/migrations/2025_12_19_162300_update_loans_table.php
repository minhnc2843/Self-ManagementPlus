<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('paid_amount', 15, 2)->default(0)->after('amount'); // Số tiền đã trả/nhận lại
            $table->text('description')->nullable()->after('status'); // Mô tả
            $table->timestamp('completed_at')->nullable()->after('updated_at'); // Ngày tất toán thực tế
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'description', 'completed_at']);
        });
    }
};