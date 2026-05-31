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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'transaction_date']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->index(['user_id', 'start_time']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->index(['created_by', 'start_time']);
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->index(['user_id', 'is_completed']);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->index(['user_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'due_date']);
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_completed']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['created_by', 'start_time']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'start_time']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'transaction_date']);
        });
    }
};
