<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('api_token', 80)->unique()->nullable();
        });

        // Seed api_token for existing clients
        $clients = DB::table('clients')->get();
        foreach ($clients as $client) {
            DB::table('clients')->where('id', $client->id)->update([
                'api_token' => bin2hex(random_bytes(30))
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('api_token');
        });
    }
};
