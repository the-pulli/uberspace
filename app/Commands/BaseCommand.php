<?php

namespace App\Commands;

use App\Exceptions\ConsoleException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use LaravelZero\Framework\Commands\Command;

use function Termwind\render;

abstract class BaseCommand extends Command
{
    protected string $user;

    protected string $homeDir;

    protected string $htmlDir;

    protected ?string $project = null;

    protected ?string $projectDir = null;

    protected ?string $projectCurrentDir = null;

    protected string $supervisorDir;

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
            'supervisorctl status',
        ]);
    }

    protected function executeCommands(string|array $commands, ?string $dir = null, bool $printOutput = true): Stringable
    {
        $dir = $dir ?? $this->homeDir;

        return str(
            collect($commands)->map(function (string $cmd) use ($dir, $printOutput) {
                return Process::path($dir)->run($cmd, function (string $type, string $output) use ($printOutput) {
                    if ($printOutput) {
                        if ($type === 'out') {
                            $this->line($output);
                        } else {
                            $this->error($output);
                        }
                    }
                })->output();
            })->join("\n")
        );
    }

    protected function renderMessage(string $message, ?string $title = null): void
    {
        if (is_null($title)) {
            $title = str($this->signature)->match('/^([\w\-:]+)/')->toString();
        }

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
            $name = $this->loopQuestion('What is the name of your project?');
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
        $this->projectCurrentDir = "$this->projectDir/current";
    }

    protected function loopQuestion(string $question, ?string $default = null): string
    {
        $result = $this->ask($question, $default);

        while (is_null($result)) {
            $result = $this->ask($question, $default);
        }

        return $result;
    }
}
