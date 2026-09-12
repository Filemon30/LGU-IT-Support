<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCategoryIssueSeeder extends Seeder
{
    public function run(): void
    {
        // Priority Levels
        $priorities = [
            ['priority_name' => 'Critical', 'priority_description' => 'System down, major business impact', 'response_target_minutes' => 30, 'resolution_target_minutes' => 240],
            ['priority_name' => 'High', 'priority_description' => 'Major functionality impaired', 'response_target_minutes' => 60, 'resolution_target_minutes' => 480],
            ['priority_name' => 'Medium', 'priority_description' => 'Minor functionality impaired', 'response_target_minutes' => 240, 'resolution_target_minutes' => 1440],
            ['priority_name' => 'Low', 'priority_description' => 'General inquiry or minor issue', 'response_target_minutes' => 480, 'resolution_target_minutes' => 2880],
        ];

        foreach ($priorities as $p) {
            DB::table('priority_levels')->updateOrInsert(
                ['priority_name' => $p['priority_name']],
                $p
            );
        }

        // Services
        $services = [
            ['service_ref_num' => 'SVC-00001', 'service_name' => 'IT Support', 'description' => 'Information Technology Support Services', 'status' => 'Active'],
            ['service_ref_num' => 'SVC-00002', 'service_name' => 'Infrastructure', 'description' => 'Network and Hardware Infrastructure Services', 'status' => 'Active'],
        ];

        foreach ($services as $s) {
            DB::table('services')->updateOrInsert(
                ['service_ref_num' => $s['service_ref_num']],
                $s
            );
        }

        // Categories
        $categories = [
            ['service_ref_num' => 'SVC-00001', 'category_name' => 'Hardware', 'description' => 'Physical device issues', 'status' => 'Active'],
            ['service_ref_num' => 'SVC-00001', 'category_name' => 'Software', 'description' => 'Application and OS issues', 'status' => 'Active'],
            ['service_ref_num' => 'SVC-00002', 'category_name' => 'Network', 'description' => 'Network connectivity issues', 'status' => 'Active'],
            ['service_ref_num' => 'SVC-00001', 'category_name' => 'Other', 'description' => 'Other IT-related issues', 'status' => 'Active'],
        ];

        foreach ($categories as $c) {
            $serviceId = DB::table('services')->where('service_ref_num', $c['service_ref_num'])->value('service_id');
            DB::table('categories')->updateOrInsert(
                ['category_name' => $c['category_name'], 'service_id' => $serviceId],
                [
                    'service_id' => $serviceId,
                    'category_name' => $c['category_name'],
                    'description' => $c['description'],
                    'status' => $c['status'],
                ]
            );
        }

        // Issues
        $issues = [
            // Hardware issues
            ['category_name' => 'Hardware', 'issue_name' => 'Not Working', 'description' => 'Device is not turning on or functioning', 'priority' => 'High'],
            ['category_name' => 'Hardware', 'issue_name' => 'Slow Performance', 'description' => 'Device is running slower than expected', 'priority' => 'Medium'],
            ['category_name' => 'Hardware', 'issue_name' => 'Error/Crash', 'description' => 'Device is showing errors or crashing', 'priority' => 'High'],
            ['category_name' => 'Hardware', 'issue_name' => 'Setup/Installation', 'description' => 'New device setup or installation needed', 'priority' => 'Low'],
            ['category_name' => 'Hardware', 'issue_name' => 'Replacement', 'description' => 'Device needs replacement', 'priority' => 'Medium'],
            // Software issues
            ['category_name' => 'Software', 'issue_name' => 'Not Working', 'description' => 'Application is not launching or responding', 'priority' => 'High'],
            ['category_name' => 'Software', 'issue_name' => 'Slow Performance', 'description' => 'Application is running slower than expected', 'priority' => 'Medium'],
            ['category_name' => 'Software', 'issue_name' => 'Error/Crash', 'description' => 'Application is showing errors or crashing', 'priority' => 'High'],
            ['category_name' => 'Software', 'issue_name' => 'Setup/Installation', 'description' => 'New software installation needed', 'priority' => 'Low'],
            ['category_name' => 'Software', 'issue_name' => 'License', 'description' => 'Software license issue', 'priority' => 'Medium'],
            // Network issues
            ['category_name' => 'Network', 'issue_name' => 'Not Working', 'description' => 'No network/internet connection', 'priority' => 'Critical'],
            ['category_name' => 'Network', 'issue_name' => 'Slow Performance', 'description' => 'Network is slow or intermittent', 'priority' => 'High'],
            ['category_name' => 'Network', 'issue_name' => 'Error/Crash', 'description' => 'Network errors or frequent disconnections', 'priority' => 'High'],
            ['category_name' => 'Network', 'issue_name' => 'Setup/Installation', 'description' => 'New network setup or configuration needed', 'priority' => 'Medium'],
            ['category_name' => 'Network', 'issue_name' => 'Access Request', 'description' => 'Request for network access or permissions', 'priority' => 'Low'],
            // Other issues
            ['category_name' => 'Other', 'issue_name' => 'General Inquiry', 'description' => 'General IT-related question or concern', 'priority' => 'Low'],
            ['category_name' => 'Other', 'issue_name' => 'Request', 'description' => 'General IT request', 'priority' => 'Low'],
        ];

        foreach ($issues as $i) {
            $categoryId = DB::table('categories')->where('category_name', $i['category_name'])->value('category_id');
            $priorityId = DB::table('priority_levels')->where('priority_name', $i['priority'])->value('priority_level_id');

            $refNum = 'ISS-'.str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

            DB::table('issues')->updateOrInsert(
                ['issue_name' => $i['issue_name'], 'category_id' => $categoryId],
                [
                    'category_id' => $categoryId,
                    'issue_ref_num' => $refNum,
                    'issue_name' => $i['issue_name'],
                    'description' => $i['description'],
                    'default_priority_level_id' => $priorityId,
                    'status' => 'Active',
                ]
            );
        }

        $this->command->info('Services, Categories, Issues, and Priority Levels seeded successfully!');
    }
}
