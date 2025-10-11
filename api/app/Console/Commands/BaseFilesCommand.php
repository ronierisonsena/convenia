<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BaseFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:base-files
        {name : Prefix name for files}
        {--provider : Create Provider}
        {--no-controller : Do not create controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Controller, Service, Repository, Request, Resource e optional Provider';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $hasProvider = $this->option('provider');

        // Controller
        if (! $this->option('no-controller')) {
            $this->call('make:controller', [
                'name' => "{$name}Controller --resource",
            ]);
        }

        // Service
        $this->callSilent('make:class', [
            'name' => "Services/{$name}Service",
        ]);
        $this->info("Service criado: {$name}Service");

        // Repository
        $this->callSilent('make:class', [
            'name' => "Repositories/{$name}Repository",
        ]);
        $this->info("Repository criado: {$name}Repository");

        // Request
        $this->call('make:request', [
            'name' => "{$name}Request",
        ]);

        // Resource
        $this->call('make:resource', [
            'name' => "{$name}Resource",
        ]);

        // Provider (optional)
        if ($hasProvider) {
            $this->call('make:provider', [
                'name' => "{$name}Provider",
            ]);
        }

        $this->info("Arquivos para {$name} criados com sucesso!");
    }
}
