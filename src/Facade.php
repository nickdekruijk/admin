<?php

namespace LaraPages\Admin;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return Controllers\BaseController::class;
    }
}
