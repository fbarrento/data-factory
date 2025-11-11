<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

use DateTimeInterface;
use FBarrento\DataFactory\Concerns\HasDataFactory;

readonly class Deployment
{
    /** @use HasDataFactory<DeploymentFactory> */
    use HasDataFactory;

    public function __construct(
        public string $id,
        public DeploymentStatus $status,
        public string $branchName,
        public string $commitHash,
        public string $commitMessage,
        public ?string $failureReason = null,
        public string $phpMajorVersion = '8.4',
        public bool $usesOctane = false,
        public ?DateTimeInterface $startedAt = null,
        public ?DateTimeInterface $finishedAt = null,
    ) {}

    public static function newFactory(): DeploymentFactory
    {
        return new DeploymentFactory;
    }
}
