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
        Schema::table('albums', function (Blueprint $table) {
            $table->foreignId('cover_id')->nullable()->change();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('avatar_id')->nullable()->change();
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->foreignId('cover_id')->nullable()->change();
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->foreignId('cover_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->foreignId('cover_id')->nullable(false)->change();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('avatar_id')->nullable(false)->change();
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->foreignId('cover_id')->nullable(false)->change();
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->foreignId('cover_id')->nullable(false)->change();
        });
    }
};
