<?php

require 'src/GenHelperFromZep.php';

use Wqy\GenHelperFromZep;

if (empty($argv[1])) {
    echo "type input cphalcon/phalcon dir\n";
    exit;
}

$sourceDir = $argv[1];
if (! is_dir($sourceDir)) {
    echo "$sourceDir is not dir\n";
    exit;
}

if (empty($argv[2])) {
    $outputDir = 'phalcon_' . Phalcon\Version::get();
}
else {
    $outputDir = $argv[2];
}

$zep = new GenHelperFromZep();
$zep->gen($sourceDir, $outputDir);

