<?php

namespace NickDeKruijk\Admin;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return Controllers\BaseController::class;
    }
}
