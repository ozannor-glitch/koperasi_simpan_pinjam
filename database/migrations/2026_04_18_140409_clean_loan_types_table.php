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


        // baru drop kolom
        $table->dropColumn(['user_id', 'total_amount', 'loan_type_id', 'tenor']);
    });
}

public function down()
{
    Schema::table('loan_types', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id');
        $table->bigInteger('total_amount');
        $table->unsignedBigInteger('loan_type_id');
        $table->integer('tenor');
    });
}
};
