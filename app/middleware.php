<?php
declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use Slim\App;
use Tuupola\Middleware\HttpBasicAuthentication;
use Tuupola\Middleware\JwtAuthentication;

return function (App $app) {
//    $app->add(SessionMiddleware::class);

    $app->add(HttpBasicAuthentication::class);
//    $app->add(JwtAuthentication::class);
//
//    $app->add(function ($req, $res, $next) {
//        $response = $next($req, $res);
//        return $response
//            ->withHeader('Access-Control-Allow-Origin', '*')
//            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-CSRF-Token')
//            ->withHeader('Access-Control-Expose-Headers', 'X-Total-Count')
//            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
//    });
};
