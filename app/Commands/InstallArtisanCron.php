<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Console\Command\Command;

class InstallArtisanCron extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:artisan-cron
                            {--P|project= : Project name to use}
                            {--H|honeybadger= : Honeybadger checkin id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install artisan cron';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $project = $this->projectName();

        if (! $honeybadgerId = $this->option('honeybadger')) {
            $honeybadgerId = $this->ask('Should we ping to honeybadger? Then we need the id?');
        }

        if ($this->executeCommands('crontab -l')->contains($this->htmlDir)) {
            $this->warn('Artisan cron command already installed');

            return Command::SUCCESS;
        }

        $command = "php $this->htmlDir/$project/current/artisan schedule:run";
        if ($honeybadgerId) {
            $command = "$command && curl https://api.honeybadger.io/v1/check_in/$honeybadgerId &> /dev/null";
        }

        // Keep current crontab
        // crontab -l || true
        $this->executeCommands("(echo -e 'MAILTO=\"\"\\n* * * * * $command')| crontab -");

        $this->renderMessage(message: 'Cron successfully installed');

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
