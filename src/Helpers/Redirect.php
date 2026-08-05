<?php

namespace App\Helpers;

class Redirect
{
    public static function to(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    public static function back(): never
    {
        $url = $_SERVER['HTTP_REFERER'] ?? '/';

        self::to($url);
    }
}