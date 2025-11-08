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
        Schema::create('reservation_tickets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('zone_id')->constrained('zones')->onDelete('cascade');
    $table->integer('number_of_tickets');
    $table->decimal('total_price', 10, 2)->nullable(); 
    $table->enum('payment_status', ['unpaid', 'paid', 'simulated'])->default('unpaid');
    $table->string('qr_code_path')->nullable();
    $table->string('ticket_pdf_path')->nullable();
    $table->dateTime('reservation_date')->nullable();
    $table->string('stripe_payment_intent_id')->nullable();
    $table->string('stripe_session_id')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_tickets');
    }
};
