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
      Schema::create('loan_installment_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_installment_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('amount_paid', 15, 2);
            $table->decimal('penalty', 15, 2)->default(0);

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_installment_payments');
    }
};
