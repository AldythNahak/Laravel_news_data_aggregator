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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 512);
            $table->text('content')->nullable();
            $table->string('author')->nullable();
            $table->string('source_name', 100); 
            $table->string('category', 100)->nullable();
            $table->string('url', 512)->unique(); 
            $table->string('image_url', 512)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['source_name', 'category', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
