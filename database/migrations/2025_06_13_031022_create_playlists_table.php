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
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->uuid('uuid');
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');

            $table->unsignedBigInteger('cover_id');
            $table->foreign('cover_id')
                ->references('id')
                ->on('files')
                ->onDelete('cascade');

            $table->string('name', 125);
            $table->unsignedBigInteger('total_duration');
            $table->boolean('is_public')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};
