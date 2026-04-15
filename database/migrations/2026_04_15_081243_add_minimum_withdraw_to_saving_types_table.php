<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('saving_types', function (Blueprint $table) {
            $table->integer('minimum_withdraw')->default(0)->after('minimum_amount');
        });
    }

    public function down()
    {
        Schema::table('saving_types', function (Blueprint $table) {
            $table->dropColumn('minimum_withdraw');
        });
    }
};
