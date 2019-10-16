<?php

namespace LangleyFoxall\XeroLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class Xero extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Xero';
    }
}
