<?php

include_once '../autoload.php';

use tools\Helper;
use tools\Database;
use tools\WooCommerce;

$productId = filter_input(INPUT_GET, 'product');
$database = new Database();

$data = $database->load($productId);
$data = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data['processed_data']), true);
$data = $database->prepare($data);

$woocommerce = new WooCommerce();
$result = $woocommerce->createProduct($data);

$database->complete($productId);
$database->link($productId, $result['id']);

header('Location: ' . Helper::getBaseUrl() . "public/list.php");
