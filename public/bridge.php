<?php

include_once '../autoload.php';
use tools\Database;

header('Access-Control-Allow-Origin: *');
$data = filter_input_array(INPUT_POST);

$database = new Database();
$database->save(json_decode($data['info'], true));
$database->disconnect();

http_response_code(200);

