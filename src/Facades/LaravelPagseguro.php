<?php

namespace Marcoaacoliveira\LaravelPagseguro\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelPagseguro extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravelpagseguro';
    }
}
