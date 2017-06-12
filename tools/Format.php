<?php

namespace tools;

class Format
{
    public static function getFinalPrice($data)
    {
        if (empty($data)) {
            return null;
        }
        
        $lowPrice = $data->price_low;
        $highPrice = $data->price_high;
        $lowSpecialPrice = self::get('special_price_low', $data);
        $highSpecialPrice = self::get('special_price_high', $data);
        
        if ($lowSpecialPrice) {
            if ($lowSpecialPrice == $highSpecialPrice) {
                return $lowSpecialPrice;
            } else {
                return "{$lowSpecialPrice} - {$highSpecialPrice}";
            }
        } else {
            if ($lowPrice == $highPrice) {
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
        
        return property_exists($data, $key);
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
}
