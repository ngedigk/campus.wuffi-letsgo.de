<?php

spl_autoload_register(function ($class) {

    $folders = [
        __DIR__.'/dto/',
        __DIR__.'/services/',
        __DIR__.'/repositories/',
        __DIR__.'/helpers/',
        __DIR__.'/controller/',
    ];

    foreach ($folders as $folder) {
        $file = $folder . $class . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});