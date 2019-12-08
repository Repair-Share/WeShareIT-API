<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuupola\Middleware\HttpBasicAuthentication;
use Tuupola\Middleware\HttpBasicAuthentication\PdoAuthenticator;
use Tuupola\Middleware\JwtAuthentication;

return function ($c) {
//    $c['logger'] = function (ContainerInterface $c) {
//        $settings = $c->get('settings');
//
//        $loggerSettings = $settings['logger'];
//        $logger = new Logger($loggerSettings['name']);
//
//        $processor = new UidProcessor();
//        $logger->pushProcessor($processor);
//
//        $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
//        $logger->pushHandler($handler);
//
//        return $logger;
//    };
// monolog
    $c['logger'] = function (ContainerInterface $c) {
        $settings = $c['settings']['logger'];
        $logger = new Monolog\Logger($settings['name']);
        $logger->pushProcessor(new Monolog\Processor\UidProcessor());
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };
    $c['db'] = function (ContainerInterface $c) {
        $db = $c['settings']['db'];
        $pdo = new PDO($db['dsn'], $db['user'], $db['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    };
    $c[\Tuupola\Middleware\HttpBasicAuthentication::class] = function (ContainerInterface $container) {
        return new HttpBasicAuthentication([
            "path" => "/token",
            "passthrough" => "/token/guest",
            "secure" => false,
            "relaxed" => ["admin"],
            "authenticator" => new PdoAuthenticator([
                "pdo" => $container['db'],
                "table" => "users",
                "user" => "email",
                "hash" => "hash"
            ]),
            "callback" => function (ServerRequestInterface $request, ResponseInterface $response, $arguments) use ($container) {
                $container["user"] = $arguments["user"];
            }
        ]);
    };
    $c[\Tuupola\Middleware\JwtAuthentication::class] = function (ContainerInterface $container) {
        return new JwtAuthentication([
            "path" => "/",
            "passthrough" => ["/token", "/welcome", "/upload", "/enrolment", "/payments", "/stats",
                "/auth/reset", "/auth/verifyemail"],
            "secret" => JWT_SECRET,
            "logger" => $container['logger'],
//			"secure" => (APP_ENV == "development" ? false : true), // force HTTPS for production
            "secure" => false, // disable -> scheme not always correctly set on request!
            "relaxed" => ["admin"], // list hosts allowed without HTTPS for DEV
            "error" => function (ServerRequestInterface $request, ResponseInterface $response, $arguments) {
                $data = array("error" => array( "status" => 401, "message" => $arguments["message"]));
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            },
            "rules" => [
                new JwtAuthentication\RequestMethodRule([
                    "passthrough" => ["OPTIONS"]
                ])
            ],
            "callback" => function (ServerRequestInterface $request, ResponseInterface $response, $arguments) use ($container) {
                $container->get('logger')->debug("Authentication ok for token: " . json_encode($arguments["decoded"]));
                $container->get("token")->hydrate($arguments["decoded"]);
            }
        ]);
    };
    $c[\App\Application\Actions\User\ListUsersAction::class] = function (ContainerInterface $container) {
        return new \App\Application\Actions\User\ListUsersAction($container['logger'], $container[\App\Domain\User\UserRepository::class]);
    };
    $c[\App\Application\Actions\User\ViewUserAction::class] = function (ContainerInterface $container) {
        return new \App\Application\Actions\User\ViewUserAction($container['logger'], $container[\App\Domain\User\UserRepository::class]);
    };
};
