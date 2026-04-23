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
        Schema::table('saving_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saving_transactions', function (Blueprint $table) {
            //
        });
    }
};
