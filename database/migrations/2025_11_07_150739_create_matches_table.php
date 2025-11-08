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
        Schema::create('matches', function (Blueprint $table) {
    $table->id();
    $table->string('team_one_title');  
    $table->string('team_one_image');  
    $table->string('team_two_title');  
    $table->string('team_two_image');      
    $table->dateTime('match_date');   
    $table->string('stadium')->nullable(); 
    $table->text('description')->nullable();  
     $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
