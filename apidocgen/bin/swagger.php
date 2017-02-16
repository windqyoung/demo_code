<?php


require __DIR__ . '/../src/ApiDocGen.php';

header('content-type: application/json');
header('access-control-allow-origin: *');

$host = ! empty($_GET['h']) ? $_GET['h'] : null;
$basePath = ! empty($_GET['b']) ? $_GET['b'] : null;

$parsed = [];
$doc = new ApiDocGen();

if (! empty($_GET['f'])) {
    $files = (array)$_GET['f'];
    foreach ($files as $f) {
        $parsed = array_merge($parsed, $doc->parseFile($f));
    }
}

if (! empty($_GET['d'])) {
    $dir = $_GET['d'];
    $parsed = array_merge($parsed, $doc->parseDir($dir));
}

if (isset($_GET['tags'])) {
    $tags = (array) $_GET['tags'];
    $parsed = array_filter($parsed, function ($one) use ($tags) {
        return array_intersect($tags, $one['tags']);
    });
}

$sw = $doc->genDocsSwagger($parsed, $host, $basePath);

echo json_encode($sw);

