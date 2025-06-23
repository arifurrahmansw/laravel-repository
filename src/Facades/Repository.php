<?php
namespace VendorName\Repository\Facades;
use Illuminate\Support\Facades\Facade;
/**
 * @see \VendorName\Repository\Repository
 */
class Repository extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \VendorName\Repository\Repository::class;
    }
}
