<?php

namespace Alshf\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Footman extends Facade
{
    /**
    * Get the registered name of the component.
    *
    * @return string
    */
    protected static function getFacadeAccessor()
    {
        return 'footman';
    }
}
