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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->date('spent_at');
            $table->string('category'); // MEAL, TRAVEL, HOTEL, OTHER
            $table->string('receipt_path')->nullable();
            $table->string('status')->default('draft'); // DRAFT, SUBMITTED, APPROVED, REJECTED, PAID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
