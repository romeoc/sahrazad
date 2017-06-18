<?php

include_once '../autoload.php';

use tools\Helper;
use tools\Database;

$data = filter_input_array(INPUT_POST);
if ($data) {
    $database = new Database();
    $database->save($data);
}

header('Location: ' . Helper::getBaseUrl() . "public/view.php?product={$data['product_id']}");

