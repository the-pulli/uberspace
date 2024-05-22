<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Console\Command\Command;

class DisableErrorPages extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disable:error-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable error pages';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->executeCommands('uberspace web errorpage 500 disable');

        $this->renderMessage('disable:error-pages', 'Successfully disabled.');

        return Command::SUCCESS;
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
