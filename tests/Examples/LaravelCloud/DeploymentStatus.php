<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

enum DeploymentStatus: string
{
    case Pending = 'pending';
    case Running = 'deployment.running';
    case Succeeded = 'deployment.succeeded';
    case Failed = 'deployment.failed';
}
