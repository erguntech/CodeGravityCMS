<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('domain')->nullable()->after('company_name');
            $table->date('domain_expires_at')->nullable()->after('domain');
            $table->date('ssl_expires_at')->nullable()->after('domain_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['domain', 'domain_expires_at', 'ssl_expires_at']);
        });
    }
};
