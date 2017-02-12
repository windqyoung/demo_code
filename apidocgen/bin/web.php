<?php


require __DIR__ . '/../src/GenApiDoc.php';

$doc = new GenApiDoc();

echo '<!-- gendoc {{{ -->', "\n\n\n";

if (! empty($_GET['f'])) {
    $file = $_GET['f'];
    echo $doc->genFile($file, "\n\n\n<hr>\n\n\n");
}

if (! empty($_GET['d'])) {
    $dir = $_GET['d'];
    echo $doc->genDir($dir, "\n\n\n<hr>\n\n\n");
}

echo "\n\n\n", '<!-- gendoc }}} -->';