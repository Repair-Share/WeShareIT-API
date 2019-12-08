<?php
declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;

return function ($c) {
    $c[UserRepository::class] =  function () {
        return new InMemoryUserRepository();
    };
};
