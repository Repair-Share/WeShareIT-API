<?php

namespace App\Application\Actions\Token;


use App\Application\Actions\Action;
use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Tuupola\Base62;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Application\Exception\ForbiddenException;

abstract class TokenAction extends Action
{
    protected $logger;
    protected $token;
    protected $container;

    public function __construct($logger, $token, ContainerInterface $container) {

        parent::__construct($logger);
        $this->token = $token;
        $this->container = $container;
    }

    /**
     * @param $response
     * @param $data
     * @return mixed
     */
    protected function createToken($userEmail, $forGuest = false)
    {
        $valid_scopes = Token::validScopes();

        if ($forGuest) {
            $sub = -1; // guest uses sub -1
            $requested_scopes = Token::allowedScopes('guest');
        } else {
            // lookup user
            $user = Capsule::table('users')->where('email', $userEmail)->first();
            if (null == $user) {
                return $this->response->withStatus(404);
            }
            if ('ACTIVE' != $user->state) {
                return $this->response->withStatus(403);
            }
            $sub = $user->user_id;
            $requested_scopes = Token::allowedScopes($user->role);
        }
        $scopes = array_filter($requested_scopes, function ($needle) use ($valid_scopes) {
            return in_array($needle, $valid_scopes);
        });
        $token = TokenAction::generateToken($scopes, $sub);
        $this->logger->info("Token generated with scopes " . json_encode($scopes) . " and sub " . json_encode($sub));

        $data = array();
        $data["status"] = "ok";
        $data["token"] = $token;

        return $this->response->withStatus(201)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }


    static public function generateToken($scopes, $sub, $future = null) {
        $payload = TokenAction::generatePayload($scopes, $sub, $future);

        $secret = getenv("JWT_SECRET");
        return JWT::encode($payload, $secret, "HS256");
    }

    static private function generatePayload($scopes, $sub, $future = null) {
        $now = new \DateTime();
        if (is_null($future)) { // default token validiy of 2 hours
            $future = new \DateTime("now +2 hours");
        } else {
            $maxValidity = new \DateTime("now +1 month");
            if ($future > $maxValidity) {
                throw new ForbiddenException("Token validity exceeds max validity (1 month)");
            }
        }
        // FIXME: update email verification email to add link for recovery in case token is expired (or redirect from webpage?)
        $base62 = new Base62();
        $jti = $base62->encode(random_bytes(16));
//        $jti = Base62::encode(random_bytes(16));

        $payload = [
            "iat" => $now->getTimeStamp(), 		// issued at
            "exp" => $future->getTimeStamp(),	// expiration
            "jti" => $jti,						// JWT ID
            "sub" => $sub,
            "scope" => array_values($scopes)    // drop keys of scopes array
        ];
        return $payload;
    }

}