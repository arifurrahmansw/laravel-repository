<?php
namespace VendorName\Repository;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use VendorName\Repository\Commands\RepositoryCommand;

class RepositoryServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('Repository')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_migration_table_name_table')
            ->hasCommand(RepositoryCommand::class);
    }
}
