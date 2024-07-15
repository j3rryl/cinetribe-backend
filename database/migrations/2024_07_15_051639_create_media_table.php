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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('genre_id')->nullable();
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('set null');
            $table->string('description');
            $table->string('thumbnail')->nullable();
            $table->enum('media_type', ['movie', 'music', 'sport']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
