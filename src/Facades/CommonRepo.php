<?php
namespace ArifurRahmanSw\Repository\Facades;
use Illuminate\Support\Facades\Facade;

class CommonRepo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'common.repo';
    }
}
