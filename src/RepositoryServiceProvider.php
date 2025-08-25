<?php
namespace ArifurRahmanSw\Repository;
use App\Repositories\Common\CommonRepository;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ArifurRahmanSw\Repository\Commands\RepositoryCommand;

class RepositoryServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-repository')
            ->hasCommand(RepositoryCommand::class);
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton('common.repo', function () {
            return new CommonRepository();
        });
    }

    public function bootingPackage(): void
    {
        $this->publishes([
            __DIR__ . '/stubs/RepositoryServiceProvider.stub' => app_path('Providers/RepositoryServiceProvider.php'),
        ], 'laravel-repository-provider');
    }
}
