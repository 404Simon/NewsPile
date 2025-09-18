<?php

declare(strict_types=1);

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
        Schema::create('search_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('search_profile_genre', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('search_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('search_profile_news_outlet', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('search_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('news_outlet_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('search_profile_article', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('search_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->date('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_profile_article');
        Schema::dropIfExists('search_profile_news_outlet');
        Schema::dropIfExists('search_profile_genre');
        Schema::dropIfExists('search_profiles');
    }
};
