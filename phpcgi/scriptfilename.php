<?php



print_r($_GET);
print_r($_POST);
print_r($_SERVER);

echo file_get_contents('php://input'), "\n";