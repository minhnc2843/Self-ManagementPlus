<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Bảng Nhóm chi tiêu (Giữ nguyên logic cũ nhưng thêm currency)
        Schema::create('expense_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->string('currency_code')->default('VND'); 
            $table->timestamps();
        });

        // 2. Bảng Thành viên nhóm
        Schema::create('expense_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_group_id')->constrained('expense_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
            
            // Một người chỉ thuộc 1 nhóm 1 lần
            $table->unique(['expense_group_id', 'user_id']);
        });

        // 3. Bảng Khoản chi chung (Header) - Đã cập nhật logic mới
        Schema::create('shared_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_group_id')->constrained('expense_groups')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users'); // Người nhập liệu
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('date');
            $table->decimal('total_amount', 15, 2)->default(0); // Tổng bill
            $table->timestamps();
        });

        // 4. Bảng Người trả tiền (Payers) - MỚI: Xử lý vụ nhiều người cùng trả 1 bill
        Schema::create('expense_payers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shared_expense_id')->constrained('shared_expenses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount_paid', 15, 2); // A trả 600k, B trả 400k
            $table->timestamps();
        });

        // 5. Bảng Chia tiền (Shares) - MỚI: Chi tiết trách nhiệm nợ
        Schema::create('expense_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shared_expense_id')->constrained('shared_expenses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount_owed', 15, 2); // Số tiền người này phải chịu trong bill
            // Không cần cột is_settled ở đây nữa, sẽ tính toán realtime hoặc qua bảng Settlement
            $table->timestamps();
        });

        // 6. Bảng Thanh toán nợ (Settlements) - MỚI: Quản lý trả nợ riêng biệt
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_group_id')->constrained('expense_groups')->onDelete('cascade');
            $table->foreignId('payer_id')->constrained('users'); // Người trả nợ
            $table->foreignId('payee_id')->constrained('users'); // Người nhận nợ
            $table->decimal('amount', 15, 2);
            $table->dateTime('date');
            // Trạng thái để xác nhận tiền đã về túi
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settlements');
        Schema::dropIfExists('expense_shares');
        Schema::dropIfExists('expense_payers');
        Schema::dropIfExists('shared_expenses');
        Schema::dropIfExists('expense_group_members');
        Schema::dropIfExists('expense_groups');
    }
};