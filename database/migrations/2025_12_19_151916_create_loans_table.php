<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('lent'); 
            $table->string('contact_name');
            $table->decimal('amount', 15, 2);
            $table->decimal('interest_rate', 5, 2)->default(0); 
            $table->date('loan_date');
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending'); 
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};