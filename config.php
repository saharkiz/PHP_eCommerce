<?php
// HTTP
define('HTTP_SERVER', 'http://littlethingsme.com/');

// HTTPS
define('HTTPS_SERVER', 'https://littlethingsme.com/');

// DIR
define('DIR_APPLICATION', 'catalog/');
define('DIR_SYSTEM', 'system/');
//define('DIR_IMAGE', 'image/');
define('DIR_IMAGE', '/home/littlethingsme/public_html/image/');
//define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_STORAGE', '/home/littlethingsme/storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', '**');
define('DB_PASSWORD', '**');
define('DB_DATABASE', '**');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');
