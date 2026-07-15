<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // sample
//        $schedule->command('command:{command-name}')->dailyAt('00:05')
//            ->onFailure(function ($exception) {
//                $logPath = storage_path('logs/cron/error');
//
//                // 폴더가 없으면 생성
//                if (!is_dir($logPath)) {
//                    mkdir($logPath, 0775, true);
//                }
//
//                $logFile = $logPath . '/cron-{NAME}-error-' . date('Y-m-d') . '.log';
//
//                \Log::build([
//                    'driver' => 'single',
//                    'path' => $logFile,
//                ])->error('{NAME} 에러: ' . $exception->getMessage(), [
//                    'exception' => $exception
//                ]);
//            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
