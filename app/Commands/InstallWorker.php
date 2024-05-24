<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Console\Command\Command;

class InstallWorker extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:worker
                            {--P|project= : Project name to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the worker.ini for supervisord';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $project = $this->projectName();

        $this->installSupervisorIni('worker.ini', $project);

        $this->renderMessage(message: 'Worker ini successfully installed');

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
