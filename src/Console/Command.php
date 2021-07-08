<?php

namespace Victorlopezalonso\LaravelUtils\Console;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Process\Process;
use Illuminate\Console\Command as LaravelCommand;

class Command extends LaravelCommand
{
    protected function list(string $icon, string $message, array $list)
    {
        system('clear');

        $this->line("$icon <fg=yellow;options=bold>{$message}</>");

        $this->line(str_pad('', strlen($icon.$message), '-'));

        $this->newLine();

        foreach ($list as $key => $value) {
            $this->line("👉 <fg=green>{$key}=</><fg=default>{$value}</>");
        }
    }

    /**
     * @param Process $process
     * @return string|string[]|null
     */
    private function exec(Process $process)
    {
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            $this->error($process->getErrorOutput());
            die();
        }

        return preg_replace("/\n/", '', $process->getOutput());
    }

    /**
     * @param $commands
     * @return string
     */
    protected function shellExec($commands)
    {
        $process = new Process($commands);

        return $this->exec($process);
    }

    /**
     * @param $command
     * @return string
     */
    protected function shellExecFromCommandLine($command)
    {
        $process = Process::fromShellCommandline($command);

        return $this->exec($process);
    }

    /**
     * @param $binary
     * @param $commands
     */
    private function shellExecWithBinary($binary, $commands)
    {
        $prefix = $this->shellExecFromCommandLine("which {$binary}");

        foreach ($commands as $command) {
            $this->shellExecFromCommandLine("{$prefix} {$command}");
        }
    }

    /**
     * @param $commands
     */
    protected function composerExec($commands)
    {
        $this->shellExecWithBinary('composer', $commands);
    }

    /**
     * @param $commands
     */
    protected function gitExec($commands)
    {
        $this->shellExecWithBinary('git', $commands);
    }

    protected function createSupervisorConfigFile($name, $command, $dir)
    {
        if (!is_dir($dir)) {
            $this->shellExecFromCommandLine("sudo mkdir -p {$dir}");
        }

        // if (!is_dir($dir)) {
        //     $this->error('Directory ' . $dir . ' does not exist');
        //     return;
        // }

        $processesByEnvironment = [
            config('laravel-utils.environments.develop') => 2,
            config('laravel-utils.environments.staging') => 3,
            config('laravel-utils.environments.production') => 10,
        ];

        $path = base_path();
        $appName = $name . '.' . App::environment();
        $numberOfProcesses = $processesByEnvironment[App::environment()] ?? 1;

        $file = [
            "[program: {$appName}]",
            'process_name=%(program_name)s_%(process_num)02d',
            "command=php {$path}/artisan {$command}",
            'autostart=true',
            'autorestart=true',
            'user=' . get_current_user(),
            "numprocs={$numberOfProcesses}",
            'redirect_stderr=true',
            "stdout_logfile=$path/storage/logs/worker{$appName}.log"
        ];

        $file = implode(PHP_EOL, $file);

        $filePath = realpath($dir) . "/{$appName}.conf";

        // create supervisor conf file
        $this->shellExecFromCommandLine('echo ' . escapeshellarg($file) . " | sudo tee -a ${filePath}");
    }

    /**
     * Update the value of an environment variable in the .env file
     *
     * @param $key
     * @param $newValue
     * @param null $environment
     */
    public function setEnvironmentValue($key, $newValue, $environment = null)
    {
        $environment = $environment ? '.' . $environment : null;

        $envFile = App::environmentFilePath() . ($environment);

        putenv("{$key}={$newValue}");

        file_put_contents(
            $envFile,
            preg_replace("/^{$key}=(.*)/m", "{$key}={$newValue}", file_get_contents($envFile))
        );
    }

    /**
     * Update the .env file from an array of $key => $value pairs.
     *
     * @param array $updatedValues
     * @return void
     */
    public function updateEnvironmentFile($updatedValues)
    {
        foreach ($updatedValues as $key => $value) {
            $this->setEnvironmentValue($key, $value);
        }
    }

    public static function isValidDeployKey($key)
    {
        return Crypt::decrypt($key) === env('APP_KEY');
    }

    public static function deploy()
    {
        $rootPath = base_path();
        $process = Process::fromShellCommandline('cd ' . $rootPath . '; ./deploy.sh');
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });
    }
}
