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
            $table->string('ext_foto');
            $table->string('ext_doc');
            $table->string('ext_vid');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orangs', function (Blueprint $table) {
            //
        });
    }
};
