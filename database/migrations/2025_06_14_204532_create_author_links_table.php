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
        Schema::create('author_links', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->uuid();

            $table->unsignedBigInteger('author_id')->nullable();
            $table->foreign('author_id')
                ->references('id')
                ->on('authors')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('url');
            $table->string('image_id');
            $table->unsignedBigInteger('order');
            $table->boolean('is_visible')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_links');
    }
};
