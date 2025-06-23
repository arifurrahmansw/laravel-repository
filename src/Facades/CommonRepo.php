<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class CommonRepo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Repositories\Common\CommonRepository::class;
    }
}
