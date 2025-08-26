<?php

namespace ArifurRahmanSw\Repository\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ArifurRahmanSw\Repository\RepositoryServiceProvider;

class RepositoryCommand extends Command
{
    protected $signature = 'make:repo {name} {--no-model}';
    protected $description = 'Generate repository interface, class, controller, requests, and optionally a model. Auto-bind in RepositoryServiceProvider.';

    public function handle(): int
    {

        // $this->generateTrait(); // ensures RepoResponse trait exists

        $name = Str::studly($this->argument('name'));
        $noModel = $this->option('no-model');

        if (! $this->confirm("Generate repository for '{$name}'?")) {
            $this->info('❌ Cancelled.');
            return self::FAILURE;
        }

        if (! $noModel) {
            $this->generateModel($name);
        }

        $this->generateRepository($name);
        $this->appendBindingToProvider($name);

        $this->info("🎉 Repository structure for '{$name}' has been generated successfully.");
        return self::SUCCESS;
    }

    protected function generateModel(string $name): void
    {
        $modelPath = app_path("Models/{$name}.php");
        $stubPath  = __DIR__ . '/../stubs/Model.stub';

        if (File::exists($modelPath)) {
            $this->warn("ℹ️ Model already exists: App\\Models\\{$name}");
            return;
        }

        if (!File::exists($stubPath)) {
            $this->error("❌ Missing stub: {$stubPath}");
            return;
        }

        $stubContents = File::get($stubPath);
        $replaced = str_replace('{{modelName}}', $name, $stubContents);

        File::ensureDirectoryExists(dirname($modelPath));
        File::put($modelPath, $replaced);

        $this->info("✅ Model created from stub: App\\Models\\{$name}");
    }


    protected function generateRepository(string $name): void
    {
        $repoPath = app_path("Repositories/{$name}");

        if (! File::exists($repoPath)) {
            File::makeDirectory($repoPath, 0755, true);
        }

        $stubPath = __DIR__ . '/../stubs';
        $stubMap = [
            'Interface.stub'     => "{$name}Interface.php",
            'Abstract.stub'      => "{$name}Abstract.php",
            'Controller.stub'    => "{$name}Controller.php",
            'StoreRequest.stub'  => "Store{$name}Request.php",
            'UpdateRequest.stub' => "Update{$name}Request.php",
        ];

        foreach ($stubMap as $stub => $fileName) {
            $stubFullPath = "{$stubPath}/{$stub}";

            if (! File::exists($stubFullPath)) {
                $this->error("❌ Missing stub: {$stubFullPath}");
                continue;
            }

            $contents = File::get($stubFullPath);

            $replaced = str_replace(
                [
                    '{{ ClassName }}',
                    '{{ className }}',
                    '{{ namespace }}',
                    '{{ repositoryNamespace }}',
                    '{{ requestNamespace }}', // <-- replace this
                    '{{ variable }}',
                    '{{ viewPath }}',
                ],
                [
                    $name,
                    Str::camel($name),
                    "App\\Repositories\\{$name}",
                    "App\\Repositories\\{$name}",
                    "App\\Http\\Requests",  // actual namespace for requests
                    Str::camel($name),
                    Str::kebab(Str::plural($name)),
                ],
                $contents
            );
            if (Str::contains($stub, 'Controller.stub')) {
                $filePath = app_path("Http/Controllers/{$fileName}");
            } elseif (Str::contains($stub, 'Request.stub')) {
                $filePath = app_path("Http/Requests/{$name}/{$fileName}");
            } else {
                $filePath = "{$repoPath}/{$fileName}";
            }

            File::ensureDirectoryExists(dirname($filePath));
            File::put($filePath, $replaced);

            $this->info("📄 Created: {$filePath}");
        }
    }

    protected function appendBindingToProvider(string $name): void
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');

        if (! File::exists($providerPath)) {
            $this->warn("⚠️ RepositoryServiceProvider not found. Auto-generating...");
            $this->generateServiceProvider();
        }

        $content = File::get($providerPath);
        $interface = "App\\Repositories\\{$name}\\{$name}Interface";
        $repository = "App\\Repositories\\{$name}\\{$name}Abstract";
        $bindingCode = "        \$this->app->bind(\\{$interface}::class, \\{$repository}::class);";

        if (Str::contains($content, $bindingCode)) {
            $this->warn("⚠️ Binding already exists in RepositoryServiceProvider.");
            return;
        }

        if (preg_match('/public function register\(\)\s*\{\s*\n/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $insertPos = $matches[0][1] + strlen($matches[0][0]);
            $newContent = substr_replace($content, $bindingCode . "\n", $insertPos, 0);
            File::put($providerPath, $newContent);
            $this->info("🔧 Bound {$interface} to {$repository} in RepositoryServiceProvider.");
        } else {
            $this->warn("⚠️ Could not locate 'register()' method in RepositoryServiceProvider.");
        }
    }

    protected function generateTrait(): void
    {
        $traitPath = __DIR__ . '/../Traits/RepoResponse.php';
        $stubPath = __DIR__ . '/../stubs/RepoResponse.stub';

        if (File::exists($traitPath)) {
            $this->warn("ℹ️ Trait already exists: ArifurRahmanSw\\Repository\\Traits\\RepoResponse");
            return;
        }

        if (!File::exists($stubPath)) {
            $this->error("❌ Missing stub: {$stubPath}");
            return;
        }

        $stubContents = File::get($stubPath);
        File::ensureDirectoryExists(dirname($traitPath));
        File::put($traitPath, $stubContents);

        $this->info("✅ Trait created: ArifurRahmanSw\\Repository\\Traits\\RepoResponse");
    }


    protected function generateServiceProvider(): void
    {
        $providerContent = <<<PHP
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Auto bindings will be inserted here
    }

    public function boot(): void
    {
        //
    }
}
PHP;

        File::put(app_path('Providers/RepositoryServiceProvider.php'), $providerContent);
        $this->info("✅ RepositoryServiceProvider created.");
    }
}
