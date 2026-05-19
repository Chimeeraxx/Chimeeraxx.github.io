<?php

declare(strict_types=1);

//automatycznie wczytuje klasy
spl_autoload_register(function (string $class): void {
    //namespace app
    $prefix = 'App\\';
    $baseDir = __DIR__.'/lib/';

    //jesli klasa nie nalezy do app, to nie wczytuje
    if (0 !== strpos($class, $prefix)) {
        return;
    }
    //usuwa prefix app, dostaje sciezke wzgledem lib/
    $relative = substr($class, strlen($prefix));
    $file = $baseDir.str_replace('\\', '/', $relative).'.php';

    if (file_exists($file)) {
        require $file;
    }
});
