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
     * Save product data
     * @param array $data
     */
    public function save($data)
    {
        $productId = $data['product_id'];
        $data['title'] = str_replace('"', '\"', $data['title']);
        $dataJson = str_replace("'", "\'", json_encode($data));
        
        $entry = $this->load($productId);
        $query = '';

        if (!empty($entry)) {
            $query = "UPDATE products SET original_data = '{$dataJson}', created_at = NOW() WHERE id = {$productId}";
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
    
    public function listing()
    {
        $query = "SELECT * FROM products ORDER BY created_at DESC";
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

