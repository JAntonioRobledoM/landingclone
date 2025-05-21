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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('username', 45)->unique();
        $table->string('email', 255)->unique();
        $table->string('password');
        $table->string('role', 50)->default('user');
        $table->string('first_name', 100)->nullable();
        $table->string('last_name', 100)->nullable();
        $table->date('birthday')->nullable();
        $table->string('gender', 45)->nullable();
        $table->text('profile_picture')->nullable();
        $table->text('banner_url')->nullable();
        $table->text('description')->nullable();
        $table->string('id_number', 45)->nullable();
        $table->string('tlf', 45)->nullable();
        $table->integer('profile_views')->default(0);
        $table->boolean('is_active')->default(true);
        $table->rememberToken();
        $table->timestamps(0);
    });
}


   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
       Schema::dropIfExists('users');
   }
};
