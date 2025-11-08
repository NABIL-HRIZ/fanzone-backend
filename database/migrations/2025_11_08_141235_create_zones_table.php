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
        Schema::create('zones', function (Blueprint $table) {
    $table->id();
    $table->foreignId('matche_id')->nullable()->constrained('matches')->nullOnDelete();
    $table->string('name');
    $table->string('city');
    $table->string('address')->nullable();
    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();
    $table->integer('capacity')->default(0);
    $table->integer('available_seats')->default(0);
    $table->enum('type', ['vip', 'standard', 'famille'])->default('standard');
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
