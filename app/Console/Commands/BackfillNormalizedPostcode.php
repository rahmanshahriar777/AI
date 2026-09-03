<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillNormalizedPostcode extends Command
{
    protected $signature = 'postcode:normalize';
    protected $description = 'Backfill normalized_postcode column for uk_postcodes table';

    public function handle()
    {
        $this->info("Starting backfill...");

        $total = DB::table('uk_postcodes')->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        DB::table('uk_postcodes')
            ->select('postcode')
            // ->whereNull('normalized_postcode') // Only update missing ones
            ->orderBy('postcode')
            ->chunk(1000, function ($rows) use ($bar) {
                $updates = [];

                foreach ($rows as $row) {
                    $normalized = strtoupper(str_replace(' ', '', $row->postcode));
                    $updates[$row->postcode] = $normalized;
                }

                // Bulk update using raw query for better performance
                foreach ($updates as $original => $normalized) {
                    DB::table('uk_postcodes')
                        ->where('postcode', $original)
                        ->update(['normalized_postcode' => $normalized]);
                    
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);
        $this->info("Backfill complete.");
    }
}
