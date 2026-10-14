<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('colors')) {
            Schema::create('colors', function (Blueprint $table) {
                $table->unsignedBigInteger('color_id')->primary();
                $table->string('color_name');
                $table->string('color_value');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->unsignedInteger('role_id')->primary();
                $table->string('role_name');
            });
        }

        if (!Schema::hasTable('priority_levels')) {
            Schema::create('priority_levels', function (Blueprint $table) {
                $table->unsignedInteger('priority_level_id')->primary();
                $table->string('priority_name');
                $table->string('priority_description', 500)->nullable();
                $table->unsignedInteger('response_target_minutes')->nullable();
                $table->unsignedInteger('resolution_target_minutes')->nullable();
            });
        }

        if (!Schema::hasTable('user_informations')) {
            Schema::create('user_informations', function (Blueprint $table) {
                $table->unsignedInteger('user_info_id')->primary();
                $table->string('first_name', 100);
                $table->string('last_name', 100);
                $table->string('middle_name', 100)->nullable();
                $table->string('suffix', 20)->nullable();
                $table->string('gender');
                $table->date('birth_date');
                $table->string('barangay');
                $table->string('city');
                $table->string('province');
                $table->string('contact_number', 20);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_accounts')) {
            Schema::create('user_accounts', function (Blueprint $table) {
                $table->unsignedInteger('user_acc_id')->primary();
                $table->string('email')->unique();
                $table->string('password_hash');
                $table->dateTime('last_login_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'staff_ref_num')) {
            Schema::dropIfExists('users');
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->primary();
                $table->string('staff_ref_num', 50)->unique();
                $table->unsignedInteger('role_id');
                $table->unsignedInteger('user_info_id')->unique();
                $table->unsignedInteger('user_acc_id')->unique();
                $table->string('status')->default('Active');
                $table->timestamps();

                $table->foreign('role_id')->references('role_id')->on('roles');
                $table->foreign('user_info_id')->references('user_info_id')->on('user_informations');
                $table->foreign('user_acc_id')->references('user_acc_id')->on('user_accounts');
            });
        }

        if (!Schema::hasTable('barangays')) {
            Schema::create('barangays', function (Blueprint $table) {
                $table->unsignedInteger('barangay_id')->primary();
                $table->string('barangay_ref_num', 50)->unique();
                $table->string('barangay_name')->unique();
                $table->string('secret_key_hash');
                $table->string('key_status')->default('Active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('offices')) {
            Schema::create('offices', function (Blueprint $table) {
                $table->unsignedInteger('office_id')->primary();
                $table->string('office_ref_num', 50)->unique();
                $table->string('office_name')->unique();
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('divisions')) {
            Schema::create('divisions', function (Blueprint $table) {
                $table->unsignedInteger('division_id')->primary();
                $table->unsignedInteger('office_id');
                $table->string('division_ref_num', 50)->unique();
                $table->string('division_name');
                $table->string('secret_key_hash');
                $table->string('key_status')->default('Active');
                $table->string('status')->default('Active');
                $table->timestamps();

                $table->foreign('office_id')->references('office_id')->on('offices');
                $table->unique(['office_id', 'division_name']);
            });
        }

        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->unsignedInteger('service_id')->primary();
                $table->string('service_ref_num', 50)->unique();
                $table->string('service_name');
                $table->text('description')->nullable();
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->unsignedInteger('category_id')->primary();
                $table->unsignedInteger('service_id')->nullable();
                $table->string('category_name');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->unsignedBigInteger('color_id')->nullable();
                $table->string('status')->default('Active');
                $table->timestamps();

                $table->foreign('color_id')->references('color_id')->on('colors');
                $table->foreign('service_id')->references('service_id')->on('services');
            });
        } elseif (!Schema::hasColumn('categories', 'service_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedInteger('service_id')->nullable()->after('category_id');
                $table->foreign('service_id')->references('service_id')->on('services');
            });
        }

        if (!Schema::hasTable('issues')) {
            Schema::create('issues', function (Blueprint $table) {
                $table->unsignedInteger('issue_id')->primary();
                $table->unsignedInteger('category_id');
                $table->string('issue_ref_num', 50)->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('default_priority_level_id')->nullable();
                $table->string('status')->default('Active');
                $table->timestamps();

                $table->foreign('category_id')->references('category_id')->on('categories');
                $table->foreign('default_priority_level_id')->references('priority_level_id')->on('priority_levels');
                $table->unique(['category_id', 'description']);
            });
        }

        if (!Schema::hasTable('requesters')) {
            Schema::create('requesters', function (Blueprint $table) {
                $table->unsignedInteger('requester_id')->primary();
                $table->string('requester_type');
                $table->unsignedInteger('barangay_id')->nullable()->unique();
                $table->unsignedInteger('division_id')->nullable()->unique();
                $table->string('status')->default('Active');
                $table->timestamp('created_at')->nullable();

                $table->foreign('barangay_id')->references('barangay_id')->on('barangays');
                $table->foreign('division_id')->references('division_id')->on('divisions');
            });
        }

        if (!Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->unsignedInteger('ticket_id')->primary();
                $table->string('ticket_ref_num', 50)->unique();
                $table->unsignedInteger('requester_id');
                $table->unsignedInteger('issue_id');
                $table->text('description');
                $table->unsignedInteger('priority_level_id');
                $table->unsignedInteger('assigned_to')->nullable();
                $table->string('ticket_status')->default('Pending');
                $table->dateTime('resolved_at')->nullable();
                $table->dateTime('cancelled_at')->nullable();
                $table->timestamps();

                $table->foreign('requester_id')->references('requester_id')->on('requesters');
                $table->foreign('issue_id')->references('issue_id')->on('issues');
                $table->foreign('priority_level_id')->references('priority_level_id')->on('priority_levels');
                $table->foreign('assigned_to')->references('user_id')->on('users');
            });
        }

        if (!Schema::hasTable('ticket_assignments')) {
            Schema::create('ticket_assignments', function (Blueprint $table) {
                $table->unsignedInteger('assignment_id')->primary();
                $table->unsignedInteger('ticket_id');
                $table->unsignedInteger('assigned_to');
                $table->unsignedInteger('assigned_by');
                $table->string('assignment_type')->default('Initial Assignment');
                $table->text('remarks')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->foreign('ticket_id')->references('ticket_id')->on('tickets')->onDelete('cascade');
                $table->foreign('assigned_to')->references('user_id')->on('users');
                $table->foreign('assigned_by')->references('user_id')->on('users');
            });
        }

        if (!Schema::hasTable('ticket_status_history')) {
            Schema::create('ticket_status_history', function (Blueprint $table) {
                $table->unsignedInteger('history_id')->primary();
                $table->unsignedInteger('ticket_id');
                $table->string('old_status')->nullable();
                $table->string('new_status');
                $table->unsignedInteger('changed_by');
                $table->text('remarks')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->foreign('ticket_id')->references('ticket_id')->on('tickets')->onDelete('cascade');
                $table->foreign('changed_by')->references('user_id')->on('users');
            });
        }

        if (!Schema::hasTable('ticket_comments')) {
            Schema::create('ticket_comments', function (Blueprint $table) {
                $table->unsignedInteger('comment_id')->primary();
                $table->unsignedInteger('ticket_id');
                $table->unsignedInteger('user_id');
                $table->text('comment');
                $table->timestamps();

                $table->foreign('ticket_id')->references('ticket_id')->on('tickets')->onDelete('cascade');
                $table->foreign('user_id')->references('user_id')->on('users');
            });
        }

        if (!Schema::hasTable('ticket_attachments')) {
            Schema::create('ticket_attachments', function (Blueprint $table) {
                $table->unsignedInteger('attachment_id')->primary();
                $table->unsignedInteger('ticket_id');
                $table->unsignedInteger('uploaded_by');
                $table->string('file_name');
                $table->string('file_path', 500);
                $table->string('file_type', 100)->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->foreign('ticket_id')->references('ticket_id')->on('tickets')->onDelete('cascade');
                $table->foreign('uploaded_by')->references('user_id')->on('users');
            });
        }

        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->unsignedInteger('notification_id')->primary();
                $table->string('notification_ref_num', 50)->unique();
                $table->unsignedInteger('user_id');
                $table->string('notification_title', 255)->nullable();
                $table->text('notification_message');
                $table->text('target_url')->nullable();
                $table->string('notification_status')->default('Unread');
                $table->dateTime('read_at')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('admin_transactions')) {
            Schema::create('admin_transactions', function (Blueprint $table) {
                $table->unsignedInteger('admin_transaction_id')->primary();
                $table->string('admin_transaction_ref', 50)->unique();
                $table->unsignedInteger('handled_by');
                $table->string('action_type');
                $table->string('entity_type');
                $table->unsignedInteger('entity_id');
                $table->text('description')->nullable();
                $table->text('old_values')->nullable();
                $table->text('new_values')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->foreign('handled_by')->references('user_id')->on('users');
            });
        }

        if (!Schema::hasTable('knowledge_base')) {
            Schema::create('knowledge_base', function (Blueprint $table) {
                $table->unsignedInteger('knowledge_id')->primary();
                $table->unsignedInteger('category_id');
                $table->string('knowledge_ref_num', 50)->unique();
                $table->string('knowledge_title');
                $table->text('knowledge_description');
                $table->text('troubleshooting_steps')->nullable();
                $table->unsignedInteger('created_by');
                $table->unsignedInteger('updated_by')->nullable();
                $table->string('status')->default('Active');
                $table->timestamps();

                $table->foreign('category_id')->references('category_id')->on('categories');
                $table->foreign('created_by')->references('user_id')->on('users');
                $table->foreign('updated_by')->references('user_id')->on('users');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_base');
        Schema::dropIfExists('admin_transactions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('ticket_status_history');
        Schema::dropIfExists('ticket_assignments');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('requesters');
        Schema::dropIfExists('issues');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('offices');
        Schema::dropIfExists('barangays');
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_accounts');
        Schema::dropIfExists('user_informations');
        Schema::dropIfExists('priority_levels');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('colors');
    }
};
