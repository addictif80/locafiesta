<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->text('contract_message')->nullable()->after('admin_notes');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('pdf_message')->nullable()->after('notes');
        });
        Schema::table('inspections', function (Blueprint $table) {
            $table->text('pdf_message')->nullable()->after('general_notes');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('contract_message');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('pdf_message');
        });
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropColumn('pdf_message');
        });
    }
};
