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
    
    /**
     * Create product in WooCommerce
     * 
     * @param array $data
     * @return array
     */
    public function createProduct($data)
    {
        return $this->woocommerce->post('products', $data);
    }
    
    /**
     * Get all category ids
     * 
     * @return array
     */
    public function getCategories()
    {
        $categories = $this->woocommerce->get('products/categories');
        $response = array();
        
        foreach ($categories as $category) {
            $response[$category['id']] = $category['name'];
        }
        
        return $response;
    }
}

