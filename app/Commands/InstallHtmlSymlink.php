<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Command\Command;

class InstallHtmlSymlink extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:html-symlink {--P|project}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the HTML symlink';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $project = $this->projectName();
        $projectPath = "$this->htmlDir/$project";
        $fullProjectPath = $projectPath.'/current';

        if (File::missing($fullProjectPath)) {
            $this->error('The project dir "'.$project.'" does not exist.');

            return Command::FAILURE;
        }

        $this->prepareHtmlFolder($fullProjectPath);

        return Command::SUCCESS;
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    private function prepareHtmlFolder(string $path): void
    {
        Process::path($this->htmlDir)->run([
            'rm -rf html',
            "ln -s $path/public html",
        ]);
    }
}
