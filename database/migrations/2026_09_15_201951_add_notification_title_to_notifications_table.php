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
        if (Schema::hasTable('notifications') && ! Schema::hasColumn('notifications', 'notification_title')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('notification_title', 255)->nullable()->before('notification_message');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('notification_title');
        });
    }
};
