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
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('url');
            $table->date('published_at');
            $table->foreignId('news_outlet_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('article_genre', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_genre');
        Schema::dropIfExists('articles');
    }
};
