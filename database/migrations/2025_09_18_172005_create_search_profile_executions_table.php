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
        Schema::create('search_profile_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('search_profile_id')->constrained()->onDelete('cascade');
            $table->timestamp('executed_at');
            $table->timestamp('articles_checked_until');
            $table->integer('articles_processed')->default(0);
            $table->timestamps();

            $table->index(['search_profile_id', 'executed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_profile_executions');
    }
};
