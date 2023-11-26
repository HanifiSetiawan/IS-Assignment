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
        Schema::table('orangs', function (Blueprint $table) {
            $table->unsignedBigInteger('key_id');
            $table->foreign('key_id')->references('id')->on('keys');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orangs', function (Blueprint $table) {
            $table->dropForeign(['key_id']);
            $table->dropColumn(['key_id']);
        });
    }
};
