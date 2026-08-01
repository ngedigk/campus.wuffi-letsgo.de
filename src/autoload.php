<?php

require_once __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function ($class) {

    $folders = [
        __DIR__.'/Controller/',
        __DIR__.'/Controller/Admin/',
        __DIR__.'/Services/',
        __DIR__.'/Repositories/',
        __DIR__.'/Helpers/',
        __DIR__.'/ViewModels/',
        __DIR__.'/dto/',
    ];

    foreach ($folders as $folder) {
        $file = $folder . $class . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});