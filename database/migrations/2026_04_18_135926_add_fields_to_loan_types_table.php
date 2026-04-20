<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->decimal('interest_rate_percent', 5, 2)->after('name');
            $table->bigInteger('max_plafon')->after('interest_rate_percent');
            $table->integer('max_tenor_months')->after('max_plafon');
        });
    }

    public function down()
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'interest_rate_percent',
                'max_plafon',
                'max_tenor_months'
            ]);
        });
    }
};
