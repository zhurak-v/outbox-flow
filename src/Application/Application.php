<?php

declare(strict_types=1);

namespace OutboxFlow\Application;

final readonly class Application
{

    public static function run(): void
    {
        $starter = new ApplicationStarter();
        $starter->start();
    }

}