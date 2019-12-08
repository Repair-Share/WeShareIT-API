<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

require __DIR__ . '/env.php';

return [
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            // Database settings
            'db' => [
                'dsn' => DB_DSN,
                'user' => DB_USER,
                'password' => DB_PASSWORD,
            ],
        ],
    ];