<?php

namespace VendorName\Skeleton\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
class RepositoryCommand extends Command
{
    protected $signature = 'make:repo {name} {--no-model}';

    protected $description = 'Create a repository (interface & abstract) and model, and register in RepositoryServiceProvider';

public function handle()
    {
        $name = Str::studly($this->argument('name'));
        // Ask for confirmation
        if (! $this->confirm("Do you really want to create repository structure for '{$name}'?")) {
            $this->info('Command aborted.');
            return 0; // Stop execution
        }
        // $name = $this->argument('name');
        $noModel = $this->option('no-model');
        $this->info("Received name: {$name}");
        // $this->info("No model flag: " . ($noModel ? 'true' : 'false'));
        // if (!$noModel) {
        //     $modelPath = app_path("Models/{$name}.php");
        //     if (!File::exists($modelPath)) {
        //         $this->call('make:model', ['name' => "Models/{$name}"]);
        //         $this->info("Model created: Models/{$name}.php");
        //     } else {
        //         $this->warn("Model already exists: Models/{$name}.php");
        //     }
        // }
        $repoPath = app_path("Repositories/{$name}");
        if (!File::exists($repoPath)) {
            File::makeDirectory($repoPath, 0755, true);
        }
        $stubPath = base_path('stubs/repository');
        $files = [
            'Interface.stub' => "{$name}Interface.php",
            'Abstract.stub' => "{$name}Abstract.php",
        ];
        foreach ($files as $stub => $fileName) {
            $stubFile = "{$stubPath}/{$stub}";
            if (!File::exists($stubFile)) {
                $this->error("Missing stub: {$stubFile}");
                continue;
            }
            $content = str_replace('{{ ClassName }}', $name, File::get($stubFile));
            File::put("{$repoPath}/{$fileName}", $content);
            $this->info("Created: Repositories/{$name}/{$fileName}");
        }
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');
        if (File::exists($providerPath)) {
            $this->appendBindingToProvider($providerPath, $name);
        }
        $this->info("🎉 Repository setup for '{$name}' completed.");
    }

    protected function appendBindingToProvider($path, $name)
    {
        $content = File::get($path);
        $pattern = '/\$repos\s*=\s*\[([^\]]*)\]/';
        preg_match($pattern, $content, $matches);
        if (isset($matches[1]) && !str_contains($matches[1], "{$name},{$name}")) {
            $newReposBlock = str_replace(
                $matches[1],
                trim($matches[1]) . "\n            '{$name}',",
                $matches[0]
            );
            $updated = str_replace($matches[0], $newReposBlock, $content);
            File::put($path, $updated);
            $this->info("🧩 Added '{$name},{$name}' to RepositoryServiceProvider.");
        } else {
            $this->warn("⚠️ Already registered or couldn't locate \$repos array.");
        }
    }
}
