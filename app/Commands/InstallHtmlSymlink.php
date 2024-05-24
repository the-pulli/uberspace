<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Console\Command\Command;

class InstallHtmlSymlink extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:html-symlink
                            {--P|project= : Project name to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the HTML symlink';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $project = $this->projectName();

        $this->executeCommands([
            'rm -rf html',
            "ln -s $this->projectCurrentDir/public html",
        ], $this->htmlDir);

        $this->renderMessage(message: 'Symlink successfully installed');

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
