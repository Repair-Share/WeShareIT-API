<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\Token\CreateTokenAction;
use App\Application\Actions\Token\CreateGuestTokenAction;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (App $app) {
        $app->get('', ListUsersAction::class);
        $app->get('/{id}', ViewUserAction::class);
    });


    $app->post("/token", CreateTokenAction::class);
    $app->post("/token/guest", CreateGuestTokenAction::class);

    /* This is just for debugging, not usefull in real life. */
    $app->get("/dump", function ($request, $response, $arguments) {
        print_r($this->token);
    });

};
