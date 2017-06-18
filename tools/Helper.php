<?php

namespace tools;

class Helper
{
    /**
     * Read global configuration
     * @param string $path (ie: 'database/mysql/user' will return $config['database']['mysql']['user']])
     */
    public static function getConfig($path)
    {
        $config = include (ROOT_DIR . 'config.php');
        $paths = explode('/', $path);
        
        foreach ($paths as $path) {
            $config = $config[$path];
        }
        
        return $config;
    }
    
    /**
     * Fetch Base URL
     * @return string
     */
    public static function getBaseUrl()
    {
        return self::getConfig('base_url');
    }
    
    /**
     * Get asset URL
     * 
     * @param string $type
     * @param string $file
     * @return string
     */
    public static function getAsset($type, $file) 
    {
        return self::getBaseUrl() . "public/assets/{$type}/{$file}";
    }
    
    /**
     * Render template
     * 
     * @param string $template
     * @param string $data
     */
    public static function render($template, $data)
    {
        ob_start();
        require ROOT_DIR . "public/templates/{$template}.phtml";
        ob_end_flush();
    }
}
