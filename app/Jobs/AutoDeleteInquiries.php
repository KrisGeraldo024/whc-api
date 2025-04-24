<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class AutoDeleteInquiries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = Carbon::now();
        $tables = ['Log', 'Inquiry'];
        
        for ($i = 0; $i < count($tables); $i++) { 
            $model = '\App\Models\\' . $tables[$i];
            $rows = $model::all();

            foreach ($rows as $key => $row) {
                $date = Carbon::parse($row->created_at)->format('Y-m-d');
                $diff = $now->diffInDays($date, false);
                if ($diff <= -90) {
                    $row->delete();
                }
            }
        }
    }
}
