<?php

namespace tools;
use tools\Helper;

class Database
{
    /**
     * Database credentials and other settings
     * @var array
     */
    protected $settings;
    
    /**
     * DB Connection Object
     * @var MySQL Link Identifier
     */
    protected $connection = null;
    
    /**
     * Result of the last query
     * @var mixed
     */
    protected $lastResults = null;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->settings = Helper::getConfig('database');
        $this->connect();
    }
    
    /**
     * Will create the connection for this instance
     * @throws Exception
     * @return $this
     */
    public function connect()
    {
        $this->connection = mysqli_connect(
            $this->settings['host'],
            $this->settings['user'],
            $this->settings['pass'],
            $this->settings['dbname'],
            $this->settings['port']
        );
        
        if (!$this->connection) {
            throw new \Exception($this->getLastError());
        }

        return $this;
    }

    /**
     * Will end the connection for this instance
     * @throws Exception
     * @return $this
     */
    public function disconnect()
    {
        mysqli_close($this->connection);
        return $this;
    }

    /**
     * Will count the last results from this instance
     * @throws Exception
     * @return int
     */
    public function countResults()
    {
        return mysqli_num_rows($this->lastResults);
    }

    /**
     * Will return the next record of the result-set
     * @throws Exception
     * @return mixed
     */
    public function fetch()
    {
        return mysqli_fetch_assoc($this->lastResults);
    }
    
    /**
     * Will return all records
     * @throws Exception
     * @return mixed
     */
    public function fetchAll()
    {
        return mysqli_fetch_all($this->lastResults);
    }

    /**
     * Will run specified query against the database
     * @throws Exception
     * @param string $query
     * @return mixed
     */
    public function query($query)
    {
        $this->lastResults = mysqli_query($this->connection, $query);
        return $this;
    }
    
    /**
     * Load a product
     * @param int $productId
     * @return array
     */
    public function load($productId)
    {
        $query = "SELECT * FROM products WHERE id = {$productId}";
        $data = $this->query($query)->fetch();
        
        return $data;
    }
    
    /**
     * Delete product data
     * @param int $productId
     */
    public function delete($productId)
    {
        $query = "DELETE FROM products WHERE id = {$productId}";
        $this->query($query);
        return $this;
    }
    
    /**
     * Reset product data
     * @param int $productId
     */
    public function reset($productId)
    {
        $query = "UPDATE products SET processed_data = NULL WHERE id = {$productId}";
        $this->query($query);
        return $this;
    }
    
    /**
     * Save product (from product manager)
     * @param array $data
     */
    public function save($data)
    {
        /* Prices */
        $data['price_low'] = preg_replace("/[^0-9\.]/", "", $data['price_low']);
        $data['special_price_low'] = preg_replace("/[^0-9\.]/", "", $data['special_price_low']);

        /* Attributes */
        $attributes = array();
        foreach ($data['attributes']['title'] as $key => $title) {
            $attributes[] = array(
                'title' => str_replace('"', '\"', $title),
                'value' => str_replace('"', '\"', $data['attributes']['value'][$key]),
                'is_variation' => ($data['attributes']['is_variation'][$key] === 'on')
            );
        }

        $data['attributes'] = $attributes;

        /* Variations */
        if (!empty($data['variations'])) {
            $variations = array();
            foreach ($data['variations']['name'] as $key => $name) {
                $variations[] = array(
                    'name' => str_replace('"', '\"', $name),
                    'available_quantity' => $data['variations']['available_quantity'][$key],
                    'price' => $data['variations']['price'][$key],
                    'special_price' => $data['variations']['special_price'][$key],
                    'advertised' => $data['variations']['advertised'][$key],
                    'final_price' => $data['variations']['final_price'][$key],
                    'image' => $data['variations']['image'][$key]
                );
            }

            $data['variations'] = $variations;
        }
        
        $data['short_description'] = str_replace('"', '\"', $data['short_description']);
        $data['description'] = str_replace('"', '\"', $data['description']);
        $data['short_description'] = str_replace(PHP_EOL, '<br />', $data['short_description']);
        $data['description'] = str_replace(PHP_EOL, '<br />', $data['description']);
        
        $productId = $data['product_id'];
        $dataJson = str_replace("'", "\'", json_encode($data));
        
        $query = "UPDATE products SET processed_data = '{$dataJson}', last_status_change = NOW() WHERE id = {$productId}";
        $this->query($query);

        $errors = $this->getLastError();
        if ($errors) {
            die($errors);
        }
        
        return $this;
    }
    
    /**
     * Set external product id
     * 
     * @param int $internalId
     * @param int $externalId
     * @return $this
     */
    public function link($internalId, $externalId)
    {
        $query = "UPDATE products SET external_id = {$externalId} WHERE id = {$internalId}";
        $this->query($query);
        
        return $this;
    }
    
    /**
     * Mark product as imported
     * 
     * @param int $productId
     * @return $this
     */
    public function complete($productId)
    {
        $query = "UPDATE products SET is_imported = 1, last_status_change = NOW(), imported_at = NOW() WHERE id = {$productId}";
        
        $this->query($query);
        $errors = $this->getLastError();
        
        if ($errors) {
            die($errors);
        }
        
        return $this;
    }
    
    /**
     * Save product data for the first time (through AliExpress bridge)
     * @param array $data
     */
    public function create($data)
    {
        $productId = $data['product_id'];
        $data['title'] = str_replace('"', '\"', $data['title']);
        $dataJson = str_replace("'", "\'", json_encode($data));
        
        $entry = $this->load($productId);
        $query = '';

        if (!empty($entry)) {
            $query = "UPDATE products SET original_data = '{$dataJson}', last_status_change = NOW() WHERE id = {$productId}";
        } else {
            $query = "INSERT INTO products(id, original_data) VALUES ({$productId}, '{$dataJson}')";
        }
        
        $this->query($query);
        $errors = $this->getLastError();        
        if ($errors) {
            http_response_code(400);
            die($errors);
        }
        
        return $this;
    }
    
    /**
     * Prepare data for API call
     * 
     * @param array $data
     * @return array
     */
    public function prepare($data)
    {
        $response = array('name' => $data['title']);
        
        if (!empty($data['short_description'])) {
            $response['short_description'] = $data['short_description'];
        }
        
        if (!empty($data['description'])) {
            $response['description'] = $data['description'];
        }
        
        if (!empty($data['categories'])) {
            $categories = array();
            foreach ($data['categories'] as $categoryId) {
                $categories[] = array('id' => $categoryId);
            }
            
            $response['categories'] = $categories;
        }
        
        $images = array(); $position = 0;
        foreach ($data['images'] as $image) {
            $images[] = array(
                'src' => $image,
                'position' => $position++
            );
        }
        $response['images'] = $images;

        
        $isVariable = (!empty($data['variations']));
        $response['type'] = ($isVariable) ? 'variable' : 'simple';
        
        if (!$isVariable) {
            $response['regular_price'] = $data['price_low'];
            if (!empty($data['special_price_low'])) {
                $response['sale_price'] = $data['special_price_low'];
            }
        }

        $attributes = $variationAttributeTitles = array(); $position = 0;
        foreach ($data['attributes'] as $attribute) {
            $attributes[] = array(
                'name' => $attribute['title'],
                'position' => $position++,
                'visible' => true,
                'options' => explode('|', $attribute['value']),
                'variation' => ($attribute['is_variation'])
            );
            
            if ($attribute['is_variation']) {
                $variationAttributeTitles[] = $attribute['title'];
            }
        }
        $response['attributes'] = $attributes;
        
        if ($isVariable) {
            $variations = array();
            foreach ($data['variations'] as $variation) {
                $newVariation = array(
                    'regular_price' => $variation['advertised'],
                    'image' => array(
                        array(
                            'src' => $variation['image'],
                            'position' => 0
                        )
                    )
                );
                
                if (!empty($variation['final_price'])) {
                    $newVariation['sale_price'] = $variation['final_price'];
                }
                
                $attributes = array(); 
                $attributeValues = explode('|', $variation['name']);
                foreach ($variationAttributeTitles as $key => $variationAttributeTitle) {
                    $attributes[] = array(
                        'name' => $variationAttributeTitle,
                        'option' => $attributeValues[$key]
                    );
                }
                
                $newVariation['attributes'] = $attributes;
                $variations[] = $newVariation;
            }
            
            $response['variations'] = $variations;
        }
        
        return $response;
    }
    
    public function listing()
    {
        $query = "SELECT * FROM products ORDER BY last_status_change DESC";
        $this->query($query);
        
        return $this->fetchAll();
    }

    /**
     * Will return the last error from this connection
     * @return string
     */
    protected function getLastError()
    {
        return mysqli_error($this->connection);
    }
}

