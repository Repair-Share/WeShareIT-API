<?php

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/../');
    $dotenv->load();
}

if (!defined('APP_ENV')) {
    $appEnv = getenv('APP_ENV');
    define('APP_ENV',(isset($appEnv) ? $appEnv : 'production') ); // defaults to production
}
if (!defined('DB_DSN')) define('DB_DSN',getenv('DB_DSN'));
if (!defined('DB_HOST')) define('DB_HOST',getenv('DB_HOST'));
if (!defined('DB_NAME')) define('DB_NAME',getenv('DB_NAME'));
if (!defined('DB_USER')) define('DB_USER',getenv('DB_USER'));
if (!defined('DB_PASSWORD')) define('DB_PASSWORD',getenv('DB_PASSWORD'));
if (!defined('JWT_SECRET')) define('JWT_SECRET',getenv('JWT_SECRET'));
