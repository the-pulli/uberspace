<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command;

class SwitchPhp extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'switch:php {--d|desired-version=} {--P|project=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch PHP version to selected one';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $project = $this->option('project');
        if ($project === null) {
            if ($this->confirm('Should we use the PHP version from your composer.json file?')) {
                $this->projectName();
            } else {
                $version = $this->choice('Which PHP version would you like to use?', [
                    '8.1',
                    '8.2',
                    '8.3',
                ]);

                $this->setPhpVersion($version);

                $this->renderMessage('switch:php', 'PHP version manually set');

                return Command::SUCCESS;
            }
        } else {
            $this->setupProject($project);
        }

        $this->setPhpVersionFromComposer();

        $this->renderMessage('switch:php', 'PHP version set from composer.json');

        return Command::SUCCESS;
    }

    protected function setPhpVersionFromComposer(): void
    {
        $composerPath = "$this->projectDir/current/composer.json";

        $phpVersion = collect(File::json($composerPath))
            ->dot()
            ->get('require.php');

        $finalVersion = str($phpVersion)
            ->match('/^\D*(\d\.\d)\.?\d?$/')
            ->toString();

        $this->setPhpVersion($finalVersion);
    }

    protected function setPhpVersion(string $version): void
    {
        $this->executeCommands("uberspace tools version use php $version");
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
