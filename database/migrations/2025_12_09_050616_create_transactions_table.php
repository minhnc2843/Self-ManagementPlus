<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // 1. Người thực hiện: Liên kết với bảng users hiện có
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // 2. Loại biến động: 'income' (Thu) hoặc 'expense' (Chi)
            $table->string('type')->default('expense')->comment('income hoặc expense');

            // 3. Loại chi tiêu/Thu nhập: Người dùng tự nhập (ví dụ: Ăn uống, Lương...)
            $table->string('category')->nullable();

            // 4. Số tiền: Dùng decimal để tính toán chính xác
            $table->decimal('amount', 15, 2);

            // 5. Ngày giao dịch
            $table->date('transaction_date');

            // 6. Ghi chú (Optional)
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};