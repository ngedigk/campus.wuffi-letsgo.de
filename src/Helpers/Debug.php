<?php

namespace App\Helpers;

class Debug
{
    public static function dump(mixed $dump)
    {
        echo '<pre>' . var_export($dump, true) . '</pre>';
    }
}

            