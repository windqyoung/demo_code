<?php


use Wqy\ClassIdeHelperGenerator;

require __DIR__ . '/src/ClassIdeHelperGenerator.php';

$args = $argv;
array_shift($args);

if (count($args) == 0) {
    $prefix = 'Phalcon\\';
    $version = Phalcon\Version::get();
    $title = "Phalcon version $version";
    $filename = "_phalcon_ide_helper_$version.php";
}
else {
    $prefix = $args[0];
    $title = isset($args[1]) ? $args[1] : "$prefix Ide Helper By Wqy";
    $filename = isset($args[2]) ? $args[2] : '_ide_helper_' . preg_replace('/\W+/', '', $prefix) . '.php';
}

ClassIdeHelperGenerator::handle($prefix, $title, $filename);


