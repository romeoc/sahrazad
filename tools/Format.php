<?php

namespace tools;

class Format
{
    protected static $conditions = array(
        'woodropship_score' => array(
            'warning' => 8,
            'danger' => 6
        ),
        'available_quantity' => array(
            'warning' => 100,
            'danger' => 30
        )
    );
    
    public static function getFinalPrice($data)
    {
        if (empty($data)) {
            return null;
        }
        
        $lowPrice = self::get('price_low', $data);
        $highPrice = self::get('price_high', $data);
        $lowSpecialPrice = self::get('special_price_low', $data);
        $highSpecialPrice = self::get('special_price_high', $data);
        
        if ($lowSpecialPrice) {
            if ($lowSpecialPrice == $highSpecialPrice || !$highSpecialPrice) {
                return $lowSpecialPrice;
            } else {
                return "{$lowSpecialPrice} - {$highSpecialPrice}";
            }
        } else {
            if ($lowPrice == $highPrice || !$highSpecialPrice) {
                return $lowPrice;
            } else {
                return "{$lowPrice} - {$highPrice}";
            }
        }
    }
    
    public static function get($key, $data)
    {
        if (empty($data)) {
            return null;
        }
        
        return (self::has($key, $data)) ? $data->$key : null;
    }
    
    public static function has($key, $data)
    {
        if (empty($data)) {
            return null;
        }
        
        return property_exists($data, $key) && !empty($data->$key);
    }
    
    public static function getLatest($key, $old, $new)
    {
        if (self::has($key, $new)) {
            return $new->$key;
        } elseif (self::has($key, $old)) {
            return $old->$key;
        } else {
            return null;
        }
    }
    
    /**
     * Get status class for each attribute based on conditions
     * 
     * @param string $key
     * @param mixed $value
     * @param bool $head
     * @return string
     */
    public static function getStatusClass($key, $value, $head = true)
    {
        $class = ($head) ? 'has-' : 'form-control-';
        if (self::$conditions[$key]['danger'] > $value) {
            return $class . 'danger';
        } elseif(self::$conditions[$key]['warning'] > $value) {
            return $class . 'warning';
        } else {
            return $class . 'success';
        }
    }
    
    /**
     * Get profit margin
     * 
     * @param array $product
     */
    public function getProfitMargin($product)
    {
        if (empty($product['advertised_price'])) {
            return 0;
        }
        
        return number_format($product['advertised_price']/$product['seller_price']*100, 2);
    }
    
    public function getPercentDecrease($product)
    {
        if (empty($product['advertised_price'])) {
            return 0;
        }
        
        return number_format(($product['advertised_price'] - $product['seller_price'])/$product['advertised_price']*100, 2);
    }
}
