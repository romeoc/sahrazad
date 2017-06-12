<?php

include_once '../autoload.php';

use tools\Format;
use tools\Helper;
use tools\Database;

$database = new Database();
$rawData = $database->listing();

$processedData = array();
foreach ($rawData as $product) {
    $originalData = json_decode($product[1]);
    $modifiedData = json_decode($product[2]);
    
    $processedData[] = array(
        'id' => $product[0],
        'title' => Format::getLatest('title', $originalData, $modifiedData),
        'seller_price' => Format::getFinalPrice($originalData),
        'advertised_price' => Format::getFinalPrice($modifiedData),
        'imported' => $product[3],
        'created_at' => $product[4],
        'imported_at' => $product[5]
    );
}

Helper::render('list', $processedData);
