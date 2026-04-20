<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {

            $table->string('recipient_name')->nullable()->after('pdf');
            $table->string('bank_name')->nullable()->after('recipient_name');
            $table->string('account_number')->nullable()->after('bank_name');

            $table->string('collateral_name')->nullable()->after('account_number');
            $table->decimal('collateral_value', 15, 2)->nullable()->after('collateral_name');
            $table->string('collateral_photo')->nullable()->after('collateral_value');

        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_name',
                'bank_name',
                'account_number',
                'collateral_name',
                'collateral_value',
                'collateral_photo'
            ]);
        });
    }
};
