<?php

declare(strict_types=1);

namespace OutboxFlow\Application;

use OutboxFlow\Infrastructure\Server\SymphonyKernel;

final readonly class ApplicationStarter
{

    private SymphonyKernel $kernel;

    public function __construct() {
        $this->kernel = new SymphonyKernel('dev', true);
    }

    public function start(): void {
        $this->kernel->boot();
    }

}