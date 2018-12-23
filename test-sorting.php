<?php

function gAutoLoader($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    if (is_readable($class . '.php')) {
        include_once $class . '.php';
        return true;
    }

    return false;
}

spl_autoload_register('gAutoLoader');


$path = realpath(__DIR__.'/../g-backup-2/test-dir/source/backup5');

$sortFunction = function($value) { return ($value->isDir() ? '2#' : '1#').$value->getPathName(); };

$directory = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
$sorted = new \directoryWalker\RecursiveSortingIterator($directory, $sortFunction );

$tit = new RecursiveTreeIterator($filtered);
foreach( $tit as $key => $value ){
    echo $value . PHP_EOL;
}