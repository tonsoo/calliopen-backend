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
        Schema::create('song_categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('song_id');
            $table->foreign('song_id')
                ->references('id')
                ->on('songs')
                ->onDelete('cascade');

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->unique(['category_id', 'song_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_categories');
    }
};
