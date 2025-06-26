<?php

namespace ArifurRahmanSw\Repository\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RepositoryCommand extends Command
{
    protected $signature = 'make:repo {name} {--no-model}';
    protected $description = 'Generate repository interface, class, and optionally a model. Auto-bind in RepositoryServiceProvider.';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $noModel = $this->option('no-model');

        if (! $this->confirm("Generate repository for '{$name}'?")) {
            $this->info('❌ Cancelled.');
            return 0;
        }

        if (! $noModel) {
            $this->generateModel($name);
        }

        $this->generateRepository($name);
        $this->appendBindingToProvider($name);

        $this->info("🎉 Repository structure for '{$name}' has been generated successfully.");
        return 0;
    }

    protected function generateModel(string $name): void
    {
        $modelPath = app_path("Models/{$name}.php");

        if (! File::exists($modelPath)) {
            $this->call('make:model', ['name' => "Models/{$name}"]);
            $this->info("✅ Model created: App\\Models\\{$name}");
        } else {
            $this->warn("ℹ️ Model already exists: App\\Models\\{$name}");
        }
    }

    protected function generateRepository(string $name): void
    {
        $repoPath = app_path("Repositories/{$name}");

        if (! File::exists($repoPath)) {
            File::makeDirectory($repoPath, 0755, true);
        }

        $stubPath = __DIR__ . '/../stubs';
        $stubMap = [
            'Interface.stub' => "{$name}Interface.php",
            'Abstract.stub'  => "{$name}Repository.php",
        ];
        foreach ($stubMap as $stub => $fileName) {
            $stubFullPath = "{$stubPath}/{$stub}";

            if (! File::exists($stubFullPath)) {
                $this->error("❌ Missing stub: {$stubFullPath}");
                continue;
            }
            $contents = File::get($stubFullPath);
            $replaced = str_replace(
                ['{{ ClassName }}', '{{ className }}', '{{ namespace }}', '{{ modelNamespace }}', '{{ baseRepositoryNamespace }}'],
                [$name, Str::camel($name), "App\\Repositories\\{$name}", "App\\Models\\{$name}", "App\\Repositories\\BaseRepository"],
                $contents
            );

            File::put("{$repoPath}/{$fileName}", $replaced);
            $this->info("📄 Created: Repositories/{$name}/{$fileName}");
        }
    }

    protected function appendBindingToProvider(string $name): void
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');

        if (! File::exists($providerPath)) {
            $this->warn("⚠️ RepositoryServiceProvider not found.");
            return;
        }

        $content = File::get($providerPath);
        $interface = "App\\Repositories\\{$name}\\{$name}Interface";
        $repository = "App\\Repositories\\{$name}\\{$name}Repository";
        $bindingCode = "        \$this->app->bind(\\{$interface}::class, \\{$repository}::class);";

        if (Str::contains($content, $bindingCode)) {
            $this->warn("⚠️ Binding already exists in RepositoryServiceProvider.");
            return;
        }

        // Inject into register() method
        if (preg_match('/public function register\(\)\s*\{\s*\n/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $insertPos = $matches[0][1] + strlen($matches[0][0]);
            $newContent = substr_replace($content, $bindingCode . "\n", $insertPos, 0);
            File::put($providerPath, $newContent);
            $this->info("🔧 Bound {$interface} to {$repository} in RepositoryServiceProvider.");
        } else {
            $this->warn("⚠️ Could not locate 'register()' method in RepositoryServiceProvider.");
        }
    }
}
