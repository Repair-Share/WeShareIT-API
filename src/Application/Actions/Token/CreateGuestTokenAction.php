<?php
namespace App\Application\Actions\Token;
use Psr\Http\Message\ResponseInterface as Response;

class CreateGuestTokenAction extends TokenAction
{

    public function action() : Response {
        $this->logger->info("Klusbib POST '/token/guest' route");
        return $this->createToken(null, true);
    }
}