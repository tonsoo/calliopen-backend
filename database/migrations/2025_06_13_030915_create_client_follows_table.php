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
        Schema::create('client_follows', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('following_id');
            $table->foreign('following_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');
            $table->timestamp('folowing_at');

            $table->unsignedBigInteger('followed_id');
            $table->foreign('followed_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');
            $table->timestamp('folowed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_follows');
    }
};
