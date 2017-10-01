<?php

namespace tools;

use Automattic\WooCommerce\Client;
use tools\Helper;

class WooCommerce 
{
    protected $woocommerce;
    protected $categoriesFile = 'data/categories.json';
    
    public function __construct()
    {
        $options = array(
            'wp_api' => true,
            'version' => 'wc/v1',
            'timeout' => 500
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
        $filePath = ROOT_DIR . $this->categoriesFile;
        
        if (!file_exists($filePath)) {
            $categories = $this->woocommerce->get('products/categories');
            $response = array();

            foreach ($categories as $category) {
                $response[$category['id']] = $category['name'];
            }

            file_put_contents($filePath, json_encode($response));
        }
        
        $response = file_get_contents($filePath);
        return json_decode($response, true);
    }
}

