<?php
namespace App\Application\Actions\Token;
use Psr\Http\Message\ResponseInterface as Response;

class CreateTokenAction extends TokenAction
{
    public function action() : Response {
        $this->logger->info("Klusbib POST '/token' route for user with email " . $this->container["user"]->email);
        return $this->createToken($this->container["user"]->email);
    }
}