<?php

namespace App\Commands;

use App\Exceptions\ConsoleException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

abstract class BaseCommand extends Command
{
    protected string $user;

    protected string $homeDir;

    protected string $htmlDir;
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
        Process::path($this->homeDir)->run([
            'supervisorctl reread',
            'supervisorctl update',
        ]);
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

        return Str::lower($name);
    }
}
