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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->uuid('uuid');
            $table->string('name', 125);
            $table->unsignedBigInteger('duration_seconds');
            $table->unsignedBigInteger('album_id');
            $table->foreign('album_id')
                ->references('id')
                ->on('albums')
                ->onDelete('cascade');

            $table->unsignedBigInteger('cover_id');
            $table->foreign('cover_id')
                ->references('id')
                ->on('files')
                ->onDelete('cascade');

            $table->text('lyrics')->nullable();
            $table->boolean('is_explicit')->default(false);

            $table->unsignedBigInteger('view_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
