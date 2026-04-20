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
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->decimal('principal', 15, 2)->after('amount_due');
            $table->decimal('interest', 15, 2)->after('principal');
            $table->decimal('remaining_balance', 15, 2)->after('interest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->dropColumn(['principal', 'interest', 'remaining_balance']);
        });
    }
};
