<?php

include_once '../autoload.php';

use tools\Helper;
use tools\Database;

$productId = filter_input(INPUT_GET, 'product');
$database = new Database();
$rawData = $database->load($productId);

$processedData = array(
    'original' => json_decode($rawData['original_data']),
    'modified' => json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $rawData['processed_data'])),
    'imported' => $rawData['is_imported'],
    'created_at' => $rawData['created_at'],
    'imported_at' => $rawData['imported_at']
);

Helper::render('view', $processedData);

