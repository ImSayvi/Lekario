<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visit;
use Carbon\Carbon;

class CompleteExpiredVisits extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'visits:complete-expired';

    /**
     * The console command description.
     */
    protected $description = 'Automatycznie oznacza przeterminowane wizyty jako ukończone';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Znajdź wizyty które:
        // - mają status 'pending' lub 'accepted'
        // - ich end_time już minął
        $expiredVisits = Visit::whereIn('status', ['pending', 'accepted'])
            ->where('end_time', '<', Carbon::now())
            ->get();

        $count = 0;

        foreach ($expiredVisits as $visit) {
            $visit->status = 'completed';
            $visit->notes = ($visit->notes ? $visit->notes . "\n\n" : '') 
                . 'Wizyta się nie odbyła - automatycznie oznaczona jako ukończona: ' 
                . Carbon::now()->format('Y-m-d H:i:s');
            $visit->save();
            
            $count++;
        }

        $this->info("Zaktualizowano {$count} wizyt.");
        
        return 0;
    }
}