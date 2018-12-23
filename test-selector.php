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

$directoryIncludes = []; //All include
$directoryExcludes = ['~y~']; //contains "y" char
$fileIncludes = ['~\.php$~']; //extension need to be ".php'
$fileExcludes = ['~temp~']; //File name or path contains the string "temp"

$directorySelector = new \directoryWalker\selector($directoryIncludes, $directoryExcludes);
$fileSelector = new \directoryWalker\selector($fileIncludes, $fileExcludes);

$sortFunction = function($value) { return ($value->isDir() ? '2#' : '1#').$value->getPathName(); };
$filterFunction = function ($current, $key, $iterator) {
    global $directorySelector, $fileSelector;

    if ($current->isDir()) {
        return $directorySelector->isGood($current->getPathName());
    }
    else {
        return $fileSelector->isGood($current->getPathname());
    }
};

$directory = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
$sorted = new \directoryWalker\RecursiveSortingIterator($directory, $sortFunction );
$filtered = new \RecursiveCallbackFilterIterator($sorted, $filterFunction);

$tit = new RecursiveTreeIterator($filtered);
foreach( $tit as $key => $value ){
    echo $value . PHP_EOL;
}