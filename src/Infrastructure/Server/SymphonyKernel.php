<?php

declare(strict_types=1);

namespace OutboxFlow\Infrastructure\Server;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel;

final class SymphonyKernel extends Kernel
{
    use MicroKernelTrait;
}