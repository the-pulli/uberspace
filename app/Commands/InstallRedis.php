<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command;

class InstallRedis extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:redis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install redis and setup supervisord';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->confirm('Do you wish to continue?')) {
            $homeDir = $this->homeDir;
            $targetRedisIni = "$homeDir/etc/services.d/redis.ini";
            if (File::missing($targetRedisIni)) {
                File::copy(app_path('Supervisor/redis.ini'), $targetRedisIni);
                $script = app_path('ShellScripts/redis.sh');
                File::copy($script, "$homeDir/redis.sh");
                $this->executeCommands('bash redis.sh');
                File::delete("$homeDir/redis.sh");

                $this->renderMessage(message: 'Redis successfully installed');
            } else {
                $this->warn('Redis is already installed');
            }
        }

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
