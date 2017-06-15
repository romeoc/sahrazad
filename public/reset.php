<?php

include_once '../autoload.php';

use tools\Helper;
use tools\Database;

$productId = filter_input(INPUT_GET, 'product');
$database = new Database();
$rawData = $database->reset($productId);

header('Location: ' . Helper::getBaseUrl() . "public/view.php?product={$productId}");

