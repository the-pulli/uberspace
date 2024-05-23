<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Command\Command;

class MySqlSecrets extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql:secrets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows the MySQL secrets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $result = Process::path($this->homeDir)->run('my_print_defaults client');
        $password = str($result->output())->match('/--password=(.+)$/');

        $this->info('Your MySQL password:');
        $this->newLine();
        $this->line($password);
        $this->newLine();
        $this->renderMessage(message: 'Successfully printed');

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
