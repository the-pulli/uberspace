<?php

namespace App\Commands;

use App\Exceptions\ConsoleException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

use function Termwind\render;

abstract class BaseCommand extends Command
{
    protected string $user;

    protected string $homeDir;

    protected string $htmlDir;

    protected ?string $project = null;

    protected ?string $projectDir = null;

    protected string $supervisorDir;

    protected string $projectNameQuestion = 'What is your project name?';

    public function __construct()
    {
        $this->user = trim(`whoami`);
        $this->homeDir = "/home/$this->user";
        $this->htmlDir = "/var/www/virtual/$this->user";
        $this->supervisorDir = "$this->homeDir/etc/services.d";

        parent::__construct();
    }

    protected function installSupervisorIni(string $iniName, string $project): void
    {
        $ini = str(File::get(app_path("Supervisor/$iniName")))
            ->replace(['USER', 'PROJECT'], [$this->user, $project]);

        File::put("$this->supervisorDir/$iniName", $ini);
        $this->executeCommands([
            'supervisorctl reread',
            'supervisorctl update',
        ]);
    }

    protected function executeCommands(string|array $commands, $dir = null): void
    {
        $dir = $dir ?? $this->homeDir;
        collect($commands)->each(fn (string $cmd) => Process::path($dir)->run($cmd, function (string $type, string $output) {
            if ($type === 'out') {
                $this->info($output);
            } else {
                $this->error($output);
            }
        }));
    }

    protected function renderMessage(string $title, string $message): void
    {
        render(<<<"HTML"
            <div>
                <div class="px-1 bg-green-600">$title</div>
                <em class="ml-1">
                  $message
                </em>
            </div>
        HTML);
    }

    /**
     * @throws ConsoleException
     */
    protected function projectName(): string
    {
        if (! $name = $this->option('project')) {
            $name = $this->ask($this->projectNameQuestion);

            while (is_null($name)) {
                $name = $this->ask($this->projectNameQuestion);
            }
        }

        if (File::missing("$this->htmlDir/$name")) {
            throw new ConsoleException('Project dir not found.');
        }

        if (File::missing("$this->htmlDir/$name/current")) {
            throw new ConsoleException('Project not deployed yet.');
        }

        $name = Str::lower($name);
        $this->setupProject($name);

        return $name;
    }

    protected function setupProject(string $name): void
    {
        $this->project = $name;
        $this->projectDir = "$this->htmlDir/$name";
    }
}
