<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $requesterId = DB::table('requesters')->value('requester_id');
        if (! $requesterId) {
            $this->command->error('No requesters found. Run RequesterSeeder first.');
            return;
        }

        $categories = DB::table('categories')->pluck('category_id', 'category_name')->toArray();
        foreach (['Hardware', 'Software', 'Network', 'Others'] as $cat) {
            if (! isset($categories[$cat])) {
                $this->command->error("Category '{$cat}' not found.");
                return;
            }
        }

        $hwCatId = $categories['Hardware'];
        $swCatId = $categories['Software'];
        $netCatId = $categories['Network'];
        $othCatId = $categories['Others'];

        $issuesByCategory = [
            'Hardware' => DB::table('issues')->where('category_id', $hwCatId)->pluck('issue_id')->toArray(),
            'Software' => DB::table('issues')->where('category_id', $swCatId)->pluck('issue_id')->toArray(),
            'Network'  => DB::table('issues')->where('category_id', $netCatId)->pluck('issue_id')->toArray(),
            'Others'   => DB::table('issues')->where('category_id', $othCatId)->pluck('issue_id')->toArray(),
        ];

        $missing = array_filter($issuesByCategory, fn($ids) => empty($ids));
        if ($missing) {
            foreach ($missing as $cat => $_) {
                $this->command->warn("No issues for category '{$cat}'. Creating default issues...");
                $catId = $categories[$cat];
                $refNum = 'ISS-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                DB::table('issues')->insert([
                    'category_id' => $catId,
                    'issue_ref_num' => $refNum,
                    'description' => "General {$cat} issue",
                    'default_priority_level_id' => DB::table('priority_levels')->where('priority_name', 'Medium')->value('priority_level_id') ?? 1,
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $issuesByCategory[$cat] = DB::table('issues')->where('category_id', $catId)->pluck('issue_id')->toArray();
            }
        }

        $priorityIds = DB::table('priority_levels')->pluck('priority_level_id')->toArray();
        $statuses = ['Pending', 'Confirmed', 'On Progress', 'Resolved', 'Cancelled'];

        $now = Carbon::now();
        $year = (int) $now->format('Y');
        $month = (int) $now->format('n');
        $today = (int) $now->format('j');
        $daysInMonth = $now->daysInMonth;

        $refCounter = DB::table('tickets')->max('ticket_id') ?? 0;

        $tickets = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            if ($month === (int) $now->format('n') && $year === (int) $now->format('Y') && $day > $today) {
                break;
            }

            $ticketsPerDay = match (true) {
                $day <= 7   => mt_rand(3, 8),
                $day <= 14  => mt_rand(4, 10),
                $day <= 21  => mt_rand(2, 7),
                $day <= 28  => mt_rand(3, 9),
                default     => mt_rand(1, 5),
            };

            for ($t = 0; $t < $ticketsPerDay; $t++) {
                $refCounter++;
                $ticketRef = 'TKT-' . str_pad($refCounter, 6, '0', STR_PAD_LEFT);

                $categoryWeights = [
                    'Hardware' => 35,
                    'Software' => 30,
                    'Network'  => 20,
                    'Others'   => 15,
                ];

                $catName = $this->weightedRandom($categoryWeights);
                $issueId = $issuesByCategory[$catName][array_rand($issuesByCategory[$catName])];

                $priorityId = $priorityIds[array_rand($priorityIds)];
                $status = $statuses[array_rand($statuses)];

                $hour = mt_rand(8, 17);
                $minute = mt_rand(0, 59);
                $created = Carbon::create($year, $month, $day, $hour, $minute);

                $tickets[] = [
                    'ticket_ref_num' => $ticketRef,
                    'requester_id' => $requesterId,
                    'issue_id' => $issueId,
                    'description' => "Auto-generated ticket for {$catName} issue on {$created->format('M d, Y')}",
                    'priority_level_id' => $priorityId,
                    'ticket_status' => $status,
                    'created_at' => $created,
                    'updated_at' => $created,
                ];
            }
        }

        foreach (array_chunk($tickets, 50) as $chunk) {
            DB::table('tickets')->insert($chunk);
        }

        $this->command->info("Seeded " . count($tickets) . " tickets for {$now->format('F Y')}.");
    }

    private function weightedRandom(array $weights): string
    {
        $total = array_sum($weights);
        $rand = mt_rand(1, $total);
        $cumulative = 0;

        foreach ($weights as $key => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $key;
            }
        }

        return array_key_first($weights);
    }
}
