<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Console\Command\Command;

class InstallReverb extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:reverb {--P|project=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the reverb.ini for supervisord';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $project = $this->projectName();

        $this->installSupervisorIni('reverb.ini', $project);

        $this->renderMessage('install:reverb', 'Reverb ini successfully installed');

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
