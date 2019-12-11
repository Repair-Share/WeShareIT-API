<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

require __DIR__ . '/env.php';

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host' => DB_HOST, //getenv('DB_HOST'),
    'database' => DB_NAME, //getenv('DB_NAME'),
    'username' => DB_USER, //getenv('DB_USER'),
    'password' => DB_PASSWORD, //getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Set the event dispatcher used by Eloquent models... (optional)
$capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

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