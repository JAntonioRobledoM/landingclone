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
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_portfolio_piece')->default(true); // Para distinguir si es parte del portafolio del artista
            $table->boolean('is_featured')->default(false); // Para obras destacadas
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Para moderación
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index(['user_id', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};