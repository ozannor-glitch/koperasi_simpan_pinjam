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
         Schema::create('loan_approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();

            $table->integer('level'); // level approval (1,2,3,...)

            $table->foreignId('approved_by')->nullable()
                  ->constrained('users')->nullOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');

            $table->text('note')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // 🔥 biar tidak dobel level
            $table->unique(['loan_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_approvals');
    }
};
