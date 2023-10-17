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
        Schema::create('keys', function (Blueprint $table) {
            $table->id();
            $table->string('purpose');
            $table->string('iv');
            $table->string('key');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('orang_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('orang_id')->references('id')->on('orangs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keys');
    }
};
