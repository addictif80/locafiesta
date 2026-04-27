<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->date('start_date');
            $table->time('start_time')->default('09:00:00');
            $table->date('end_date');
            $table->time('end_time')->default('18:00:00');
            $table->enum('status', [
                'pending_payment', 'confirmed', 'in_progress',
                'completed', 'cancelled', 'cancelled_no_refund'
            ])->default('pending_payment');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->unsignedInteger('deposit_percentage')->default(30);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->timestamp('deposit_paid_at')->nullable();
            // Use address
            $table->boolean('use_different_address')->default(false);
            $table->string('use_address')->nullable();
            $table->string('use_postal_code', 10)->nullable();
            $table->string('use_city')->nullable();
            // Cancellation
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->boolean('deposit_refunded')->default(false);
            $table->timestamp('deposit_refunded_at')->nullable();
            // Notes
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
