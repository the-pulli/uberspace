<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Console\Command\Command;

class AddDomains extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:add
                            {domain : The domain to add}
                            {--W|www : Should add a www subdomain}
                            {--default-socket : Use the default socket domain name ws.domain.name}
                            {--S|socket= : Use a custom domain for the socket domain}
                            {--I|interactive : Starts the interactive mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new domain';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $domain = $this->option('domain');
        $www = $this->option('www');
        $defaultSocket = $this->option('default-socket');
        $socket = $this->option('socket');
        $interactive = $this->option('interactive');

        $subdomain = null;
        $webSocketDomain = null;

        if ($interactive) {
            $domain = $this->loopQuestion('What domain should we add?');

            if ($this->confirm('Should we add the www subdomain?')) {
                $subdomain = "www.$domain";
            }

            if ($this->confirm('Should we add a websocket domain?')) {
                $webSocketDomain = "ws.$domain";
                if (! $this->confirm("Should we use $webSocketDomain as websocket domain?")) {
                    $webSocketDomain = $this->loopQuestion('Which domain should we use as websocket domain?');
                }
            }
        } else {
            if (is_null($domain)) {
                $this->error('Please specify a domain.');

                return Command::FAILURE;
            }

            if ($www) {
                $subdomain = "www.$domain";
            }

            if ($defaultSocket && is_null($socket)) {
                $webSocketDomain = "ws.$domain";
            }

            if ($socket) {
                $webSocketDomain = $socket;
            }
        }

        $domains = collect([$domain, $subdomain, $webSocketDomain])
            ->whereNotNull()
            ->each(fn (string $domain) => $this->executeCommands("uberspace web domain add $domain"))
            ->map(fn (string $domain) => [$domain])
            ->toArray();

        $this->info('We added the following domain(s):');
        $this->newLine();
        $this->table(['Domain'], $domains);
        $this->newLine();
        $this->renderMessage(message: 'Domain(s) were added');

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
