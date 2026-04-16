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
        Schema::create('penarikans', function (Blueprint $table) {
             $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('member_saving_id')
                  ->constrained('member_savings')
                  ->cascadeOnDelete();

            $table->string('kode_penarikan')->unique();

            $table->decimal('jumlah', 15, 2);
            $table->decimal('biaya_admin', 15, 2)->default(0);
            $table->decimal('jumlah_diterima', 15, 2);

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'completed'
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penarikans');
    }
};
