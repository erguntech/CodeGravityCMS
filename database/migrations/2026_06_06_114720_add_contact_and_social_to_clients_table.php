<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->date('domain_started_at')->nullable()->after('domain');
            $table->date('ssl_started_at')->nullable()->after('domain_expires_at');
            // İletişim bilgileri
            $table->string('address')->nullable()->after('ssl_expires_at');
            $table->string('phone')->nullable()->after('address');
            $table->string('mobile')->nullable()->after('phone');
            // Sosyal medya
            $table->string('instagram')->nullable()->after('mobile');
            $table->string('facebook')->nullable()->after('instagram');
            $table->string('whatsapp')->nullable()->after('facebook');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'domain_started_at', 'ssl_started_at',
                'address', 'phone', 'mobile',
                'instagram', 'facebook', 'whatsapp',
            ]);
        });
    }
};
