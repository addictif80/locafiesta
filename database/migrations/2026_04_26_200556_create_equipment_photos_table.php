<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_photos');
    }
};
