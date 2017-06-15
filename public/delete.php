<?php

include_once '../autoload.php';

use tools\Helper;
use tools\Database;

$productId = filter_input(INPUT_GET, 'product');
$database = new Database();
$rawData = $database->delete($productId);

header('Location: ' . Helper::getBaseUrl() . 'public/list.php');

