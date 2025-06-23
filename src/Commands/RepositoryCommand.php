<?php
namespace ArifurRahmanSw\Repository\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class RepositoryCommand extends Command
{
    protected $signature = 'make:repo {name} {--no-model}';
    protected $description = 'Create repository interface, abstract class, optional model, and register in RepositoryServiceProvider';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $noModel = $this->option('no-model');

        if (! $this->confirm("Do you want to generate repository for '{$name}'?")) {
            $this->info('🚫 Aborted.');
            return 0;
        }

        // Step 1: Generate model if needed
        if (! $noModel) {
            $modelPath = app_path("Models/{$name}.php");
            if (! File::exists($modelPath)) {
                $this->call('make:model', ['name' => "Models/{$name}"]);
                $this->info("✅ Model created: Models/{$name}.php");
            } else {
                $this->warn("ℹ️ Model already exists: Models/{$name}.php");
            }
        }

        // Step 2: Generate repository files
        $repoPath = app_path("Repositories/{$name}");
        if (! File::exists($repoPath)) {
            File::makeDirectory($repoPath, 0755, true);
        }
        // Use stubs folder inside package src (adjust path as needed)
        $stubPath = __DIR__ . '/../../stubs';
        $stubMap = [
            'Interface.stub' => "{$name}Interface.php",
            'Abstract.stub'  => "{$name}Repository.php",
        ];

        foreach ($stubMap as $stubFile => $targetFile) {
            $stubFullPath = "{$stubPath}/{$stubFile}";

            if (! File::exists($stubFullPath)) {
                $this->error("❌ Missing stub: {$stubFullPath}");
                continue;
            }

            $contents = File::get($stubFullPath);

            $replacements = [
                '{{ ClassName }}' => $name,
                '{{ className }}' => Str::camel($name),
                '{{ namespace }}' => "App\\Repositories\\{$name}",
                '{{ modelNamespace }}' => "App\\Models\\{$name}",
                '{{ baseRepositoryNamespace }}' => "App\\Repositories\\BaseRepository",
            ];

            $replaced = str_replace(array_keys($replacements), array_values($replacements), $contents);

            File::put("{$repoPath}/{$targetFile}", $replaced);
            $this->info("📄 Created: Repositories/{$name}/{$targetFile}");
        }

        // Step 3: Append binding to service provider
        $this->appendBindingToProvider($name);

        $this->info("🎉 Repository structure for '{$name}' has been generated.");
        return 0;
    }

    protected function appendBindingToProvider(string $name): void
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');

        if (! File::exists($providerPath)) {
            $this->warn("⚠️ RepositoryServiceProvider.php not found.");
            return;
        }

        $content = File::get($providerPath);
        $interface = "App\\Repositories\\{$name}\\{$name}Interface";
        $repository = "App\\Repositories\\{$name}\\{$name}Repository";

        $bindingCode = "        \$this->app->bind(\\{$interface}::class, \\{$repository}::class);";

        // Check if binding already exists
        if (Str::contains($content, $bindingCode)) {
            $this->warn("⚠️ Binding already exists.");
            return;
        }

        // Find register method and insert binding inside its body
        $pattern = '/(public function register\(\)\s*\{\s*\n)/';

        if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $insertPos = $matches[0][1] + strlen($matches[0][0]);
            $content = substr_replace($content, $bindingCode . "\n", $insertPos, 0);

            File::put($providerPath, $content);
            $this->info("🧩 Bound {$interface} to {$repository} in RepositoryServiceProvider.");
        } else {
            $this->warn("⚠️ Could not find register() method to inject binding.");
        }
    }
}
