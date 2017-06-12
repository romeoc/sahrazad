<?php

date_default_timezone_set("UTC");
define("ROOT_DIR", __DIR__ . '/');

$targets = array('tools');
foreach ($targets as $dir) {
    require_recursive(ROOT_DIR . $dir);
}

function require_recursive($dir) 
{
    $scan = glob("{$dir}/*");
    foreach ($scan as $path) {
        if (preg_match('/\.php$/', $path)) {
            require_once $path;
        } elseif (is_dir($path)) {
            require_recursive($path);
        }
    }
}

