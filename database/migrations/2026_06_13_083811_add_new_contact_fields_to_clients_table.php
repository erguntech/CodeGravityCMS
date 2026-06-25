<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Modify existing address field to text to allow longer address strings (if doctrine/dbal allows, but usually we just keep it or add a new one. Since sqlite requires dbal to change columns, let's just make it a text field if we can, or just keep string since users can just type a lot in a text area and string holds 255 chars. I will just add the new fields).
            $table->string('additional_address')->nullable()->after('address');
            $table->string('fax')->nullable()->after('phone');
            $table->string('additional_contact')->nullable()->after('mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['additional_address', 'fax', 'additional_contact']);
        });
    }
};
