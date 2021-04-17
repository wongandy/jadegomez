<?php

namespace App\Console;

use App\Models\Sale;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            // DB::table('test')->delete();
            // Sale::where('id', 3)->update(['end_of_day_by' => 31]);

            // iterate all branches ids
            $branches = Branch::select('id')->get();
            
            foreach ($branches as $branch) {
                //check if previous day has any sales
                $previousDayHasSale = Sale::where('branch_id', '=', $branch->id)
                    ->whereBetween('created_at', [date('Y-m-d', strtotime('-1 day')) . ' 00:00:00', date('Y-m-d', strtotime('-1 day')) . ' 23:59:59'])
                    ->count();

                if ($previousDayHasSale) {
                    //check if end of day button was clicked
                    $endOfDayButtonWasClicked = Sale::where('branch_id', '=', $branch->id)
                        ->whereNotNull('end_of_day_at')
                        ->whereBetween('created_at', [date('Y-m-d', strtotime('-1 day')) . ' 00:00:00', date('Y-m-d', strtotime('-1 day')) . ' 23:59:59'])
                        ->count();

                    if (! $endOfDayButtonWasClicked) {
                        //get id of the last person who approved a sale
                        $idOfLastUser = Sale::select('approved_by')
                            ->where('branch_id', '=', $branch->id)
                            ->whereNotNull('approved_by')
                            ->latest()
                            ->first()
                            ->approved_by;

                        // assign id of last user to be responsible for clicking end of day button
                        Sale::where('branch_id', '=', $branch->id)
                            // ->whereBetween('created_at', [date('Y-m-d', strtotime('-1 day')) . ' 00:00:00', date('Y-m-d', strtotime('-1 day')) . ' 23:59:59'])
                            ->whereNull('end_of_day_by')
                            ->whereNull('end_of_day_at')
                            ->update([
                                'end_of_day_by' => $idOfLastUser,
                                'end_of_day_at' => date('Y-m-d', strtotime('-1 day')) . ' 23:59:59'
                            ]);
                    }
                }
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
