<?php

namespace Infrastructure\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ComponentMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:component {parent} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * All filetypes we care about per namespace
     *
     * @var array
     */
    protected $fileTypes = [
        'Models',
        'Controllers',
        'Services',
        'Repositories'
    ];

    /**
     */
    protected $filetype;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->makeDirectory(base_path().'/api/'.$this->argument('parent'));

        foreach ($this->fileTypes as $fileType) {
            $this->makeSubDirectories(base_path().'/api/'.$this->argument('parent'), $fileType);
            $this->makeFile($fileType);
        }
    }

    protected function makeFile($fileType)
    {
        //this is the name of the stubfile, i.e. 'model.stub'
        $stubFile = $fileType == 'Repositories' ? "repository" : strtolower(rtrim($fileType, 's'));
        //remove ending S for filename, unless it's Models, in which case we don't like Models as an appendage to the filename

        if($fileType == 'Models'){
            $this->fileName = "";
        } else if($fileType == 'Repositories'){
            $this->fileName = "Repository";
        } else {
            $this->fileName = rtrim($fileType, 's');
        }

        $name = $this->argument('name');
        if (!$this->files->exists(base_path() . '/api/' . $this->argument('parent') . '/' . $fileType . '/' . $name . $this->fileName . '.php')) {
            $class = $this->buildClass($name, $stubFile, $fileType);
            $this->files->put(base_path() . '/api/' . $this->argument('parent') . '/' . $fileType . '/' . $name . $this->fileName . '.php', $class);
        }
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeSubDirectories($path, $fileType)
    {
        if ($this->files->isDirectory($path)) {
            $this->makeDirectory($path.'/'.$fileType.'/');
        }
    }


    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @return string
     */
    protected function getModelPath($name)
    {
        $name = str_replace($this->getAppNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }


    protected function buildClass($name,$type,$fileType)
    {
        $stub = $this->files->get($this->getStub($type));
        return $this->replaceNamespace($stub, $name,  $fileType)->replaceClass($stub, $name);
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $filename = $this->fileName;
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        $stub = str_replace('DummyVariable', $class, $stub);
        $stub = str_replace('dummyVariable', lcfirst($class), $stub);

        return str_replace('DummyClass', $class.$filename, $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub($type)
    {
        return __DIR__.'/stubs/'.$type.'.stub';
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name, $fileType)
    {

        $stub = str_replace('DummyNamespace','Api\\'.$this->argument('parent').'\\'.$fileType, $stub);
        $stub = str_replace('DummyPath','Api\\'.$this->argument('parent'), $stub);

        return $this;
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    public function handle()
    {
        $this->fire();
    }
}