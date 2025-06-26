<?php
namespace ArifurRahmanSw\Repository\Facades;

use Illuminate\Support\Facades\Facade;

class Repository extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ArifurRahmanSw\Repository\BaseRepository::class;
    }
}
