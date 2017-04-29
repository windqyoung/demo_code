<?php


$post = 'PO=V1&ST=V2';
$contentLength = strlen($post);

$env = [
    'REQUEST_METHOD' => 'POST',
    'SCRIPT_FILENAME' => realpath('scriptfilename.php'),
    'REDIRECT_STATUS' => "true",
    'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
    'QUERY_STRING' => 'QUE=1&RY=2',
    'CONTENT_LENGTH' => $contentLength,
];

$desc = [
    ['pipe', 'r'],
    ['pipe', 'w'],
];

$cgiexe = str_replace('php.exe', 'php-cgi.exe', PHP_BINARY);

$fp = proc_open($cgiexe, $desc, $pipes, null, $env);

$fp0 = $pipes[0];
fwrite($fp0, $post);
fclose($fp0);

echo stream_get_contents($pipes[1]);
