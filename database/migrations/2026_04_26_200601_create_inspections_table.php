<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['departure', 'return']);
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('general_notes')->nullable();
            $table->string('client_signature_path')->nullable();
            $table->string('admin_signature_path')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
