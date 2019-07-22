<?php

namespace Infrastructure\Console;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionClass;
use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    protected function commands()
    {
        $config = config('optimus.components');

        foreach ($config['namespaces'] as $namespace => $path) {
            $subDirectories = glob(sprintf('%s%s*', $path, DIRECTORY_SEPARATOR), GLOB_ONLYDIR);

            $paths = [];

            foreach ($subDirectories as $componentRoot) {
                $component = substr($componentRoot, strrpos($componentRoot, DIRECTORY_SEPARATOR) + 1);

                $commandDirectory = sprintf('%s%sConsole', $componentRoot, DIRECTORY_SEPARATOR);

                if (file_exists($commandDirectory)) {
                    $paths[] =  $commandDirectory;
                }
            }

            if (empty($paths)) {
                continue;
            }

            foreach ((new Finder)->in($paths)->files() as $command) {
                $command = $namespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($command->getPathname(), $path)
                );

                if (is_subclass_of($command, Command::class) &&
                    ! (new ReflectionClass($command))->isAbstract()) {
                    Artisan::starting(function ($artisan) use ($command) {
                        $artisan->resolve($command);
                    });
                }
            }
        }
    }
}
