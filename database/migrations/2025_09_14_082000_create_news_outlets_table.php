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
        Schema::create('news_outlets', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('rss_url');
            $table->string('b64_logo');
            $table->timestamps();
        });

        Schema::create('news_outlet_genre', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('news_outlet_id')->constrained()->onDelete('cascade');
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_outlet_genre');
        Schema::dropIfExists('news_outlets');
    }
};
