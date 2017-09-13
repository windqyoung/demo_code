<?php

use Wqy\ProcessMakerCaseDoc;

require 'src/ProcessMakerCaseDoc.php';

$pro_uid = '75825646159a55486540be4008382175'; // 授信
// $pro_uid = '98580932659b7802b61a987031896746'; // 测试
// $dsn = 'mysql:host=10.20.45.21;dbname=yjr_workflow_test';
$dsn = 'mysql:host=10.20.45.61;dbname=credit_workflow';
// $dsn = 'mysql:host=localhost;dbname=wf_workflow_vera';
// $user = 'yf';
$pwd = '1234';
$user = 'root';
$pwd = 'root';

$pdo = new PDO($dsn, $user, $pwd);
$pdo->query('set names utf8');

$doc = new ProcessMakerCaseDoc($pdo, $pro_uid);

$doc->genBySteps();

