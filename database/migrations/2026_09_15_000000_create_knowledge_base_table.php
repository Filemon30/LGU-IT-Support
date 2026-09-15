<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_base', function (Blueprint $table) {
            $table->id('knowledge_id');
            $table->foreignId('category_id')->constrained('categories', 'category_id');
            $table->string('knowledge_ref_num', 20)->unique();
            $table->string('knowledge_title');
            $table->text('knowledge_description');
            $table->text('troubleshooting_steps')->nullable();
            $table->foreignId('created_by')->constrained('users', 'user_id');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'user_id');
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_base');
    }
};
