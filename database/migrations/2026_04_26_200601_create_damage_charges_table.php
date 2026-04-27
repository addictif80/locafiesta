<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspection_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('damage_scale_item_id')->nullable()->constrained('damage_scale_items')->nullOnDelete();
            $table->string('label');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_charges');
    }
};
