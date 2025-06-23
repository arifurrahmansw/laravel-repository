<?php

namespace ArifurRahmanSw\Repository;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ArifurRahmanSw\Repository\Commands\RepositoryCommand;
use ArifurRahmanSw\Repository\Contracts\BaseRepositoryInterface;
use ArifurRahmanSw\Repository\Repositories\UserRepository;
use ArifurRahmanSw\Repository\Repositories\CommonRepository;

class RepositoryServiceProvider extends PackageServiceProvider
{
    public function register(): void
    {
        parent::register();

        // Bind interface to concrete repository
        $this->app->bind(BaseRepositoryInterface::class, function ($app) {
            return new UserRepository(new \App\Models\User());
        });

        // Register CommonRepo for facade access
        $this->app->singleton('common.repo', function () {
            return new CommonRepository();
        });
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-repository')
            ->hasCommands([
                RepositoryCommand::class, // optional
            ]);
            // ->hasConfigFile()       // if you add config/repository.php
            // ->hasViews()            // if views are needed
            // ->hasMigrations()       // if migrations are needed
            // ->hasRoute('web');      // if you plan to ship routes
    }
}
