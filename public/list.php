<?php

include_once '../autoload.php';

use tools\Format;
use tools\Helper;
use tools\Database;

$database = new Database();
$rawData = $database->listing();

$processedData = array();
foreach ($rawData as $product) {
    $originalData = json_decode($product[2]);
    $modifiedData = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $product[3]));
    
    $processedData[] = array(
        'id' => $product[0],
        'external_id' => $product[1],
        'title' => Format::getLatest('title', $originalData, $modifiedData),
        'seller_price' => Format::getFinalPrice($originalData),
        'advertised_price' => Format::getFinalPrice($modifiedData),
        'imported' => $product[4],
        'created_at' => $product[5],
        'imported_at' => $product[6]
    );
}

Helper::render('list', $processedData);
