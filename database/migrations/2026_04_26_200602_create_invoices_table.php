<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->enum('type', ['deposit', 'balance', 'damage', 'manual'])->default('deposit');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled', 'refunded'])->default('pending');
            $table->string('pdf_path')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
