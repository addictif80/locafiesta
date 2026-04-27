<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->date('birth_date')->nullable()->after('last_name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->string('postal_code', 10)->nullable()->after('address');
            $table->string('city')->nullable()->after('postal_code');
            $table->enum('role', ['admin', 'agent', 'client'])->default('client')->after('city');
            $table->boolean('is_blacklisted')->default(false)->after('role');
            $table->text('blacklist_reason')->nullable()->after('is_blacklisted');
            $table->boolean('rgpd_consent')->default(false)->after('blacklist_reason');
            $table->timestamp('rgpd_consent_at')->nullable()->after('rgpd_consent');
            $table->timestamp('deletion_requested_at')->nullable()->after('rgpd_consent_at');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'birth_date', 'phone',
                'address', 'postal_code', 'city', 'role',
                'is_blacklisted', 'blacklist_reason',
                'rgpd_consent', 'rgpd_consent_at', 'deletion_requested_at',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
