<?php

namespace tools;

use Automattic\WooCommerce\Client;
use tools\Helper;

class WooCommerce 
{
    protected $woocommerce;
    
    public function __construct()
    {
        $options = array(
            'wp_api' => true,
            'version' => 'wc/v1'
        );
        
        if (Helper::getConfig('woocommerce/developer_mode')) {
            $options['verify_ssl'] = false;
        }
        
        $this->woocommerce = new Client(
            Helper::getConfig('woocommerce/url'),
            Helper::getConfig('woocommerce/consumer_key'),
            Helper::getConfig('woocommerce/consumer_secret'),
            $options
        );
    }
    
    public function createProduct($data)
    {
        return $this->woocommerce->post('products', $data);
    }
}

