<?php

declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Domain\Project\ProjectRepository;
use Psr\Container\ContainerInterface;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        UserRepository::class => function (ContainerInterface $c) {
            return new UserRepository($c->get(PDO::class));
        },
    ]);
};

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Usuarios
        App\Domain\User\UserRepository::class => \DI\autowire(App\Domain\User\UserRepository::class),
        // Proyectos
        ProjectRepository::class => \DI\autowire(ProjectRepository::class),
    ]);
};
