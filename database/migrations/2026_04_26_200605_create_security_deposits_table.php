<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->enum('phase', ['departure', 'return'])->default('departure');
            $table->string('holder_name');
            $table->string('bank_name');
            $table->string('bank_address');
            $table->string('check_number');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['held', 'returned', 'cashed'])->default('held');
            $table->text('notes')->nullable();
            $table->timestamp('action_at')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_deposits');
    }
};
