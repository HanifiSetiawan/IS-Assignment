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
        Schema::table('keys', function (Blueprint $table) {
            $table->dropForeign(['orang_id']);
            $table->dropColumn(['iv', 'orang_id', 'purpose']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('your_table', function (Blueprint $table) {
            $table->unsignedBigInteger('orang_id');
            $table->string('iv');
            $table->string('purpose');

            $table->foreign('orang_id')->references('id')->on('orangs');
        });
    }
};
