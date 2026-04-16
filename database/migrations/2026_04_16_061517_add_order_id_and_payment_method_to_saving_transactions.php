<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saving_transactions', function (Blueprint $table) {
            $table->string('order_id')->nullable()->after('id');
            $table->string('payment_method')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('saving_transactions', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'payment_method']);
        });
    }
};
