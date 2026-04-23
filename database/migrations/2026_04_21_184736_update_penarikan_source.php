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
        Schema::table('penarikans', function (Blueprint $table) {

        $table->string('source_type')->nullable(); // saving / loan
        $table->unsignedBigInteger('source_id')->nullable();

        $table->unsignedBigInteger('member_saving_id')->nullable()->change(); // 🔥 penting
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
