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
        Schema::create('artist_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('motivation')->nullable(); 
            
            // Campos para la obra de muestra
            $table->string('artwork_image_path')->nullable(); // Ruta de la imagen
            $table->string('artwork_title')->nullable(); // Título de la obra
            $table->text('artwork_description')->nullable(); // Descripción de la obra
            $table->string('artwork_original_filename')->nullable(); // Nombre original del archivo
            $table->string('artwork_mime_type')->nullable(); // Tipo MIME
            $table->unsignedBigInteger('artwork_file_size')->nullable(); // Tamaño del archivo
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); 
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_requests');
    }
};