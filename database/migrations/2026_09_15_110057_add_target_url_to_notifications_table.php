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
        if (Schema::hasTable('notifications') && ! Schema::hasColumn('notifications', 'target_url')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('target_url', 255)->nullable()->after('notification_message');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('target_url');
        });
    }
};
