<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class MapFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'map';
    }
}