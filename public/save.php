<?php

include_once '../autoload.php';

use tools\Helper;
use tools\Database;

$data = filter_input_array(INPUT_POST);
if ($data) {
    $database = new Database();
    
    /* Prices */
    $data['price_low'] = preg_replace("/[^0-9\.]/", "", $data['price_low']);
    $data['special_price_low'] = preg_replace("/[^0-9\.]/", "", $data['special_price_low']);
    
    /* Attributes */
    $attributes = array();
    foreach ($data['attributes']['title'] as $key => $title) {
        $attributes[] = array(
            'title' => $title,
            'value' => $data['attributes']['value'][$key],
            'is_variation' => ($data['attributes']['is_variation'][$key] === 'on')
        );
    }
    
    $data['attributes'] = $attributes;
    
    /* Variations */
    if (!empty($data['variations'])) {
        $variations = array();
        foreach ($data['variations']['name'] as $key => $name) {
            $variations[] = array(
                'name' => $name,
                'available_quantity' => $data['variations']['available_quantity'][$key],
                'price' => $data['variations']['price'][$key],
                'special_price' => $data['variations']['special_price'][$key],
                'advertised' => $data['variations']['advertised'][$key],
                'image' => $data['variations']['image'][$key]
            );
        }

        $data['variations'] = $variations;
    }
    
    $database->save($data);
}

header('Location: ' . Helper::getBaseUrl() . "public/view.php?product={$data['product_id']}");

