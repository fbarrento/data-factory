<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

use DateTimeInterface;
use FBarrento\DataFactory\Concerns\HasDataFactory;

readonly class Environment
{
    /** @use HasDataFactory<EnvironmentFactory> */
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $status,
        public string $vanityDomain,
        public string $phpMajorVersion,
        public int $nodeVersion,
        public bool $usesOctane,
        public bool $usesHibernation,
        public ?Deployment $currentDeployment = null,
        public DateTimeInterface $createdAt = new \DateTime,
    ) {}

    public static function newFactory(): EnvironmentFactory
    {
        return new EnvironmentFactory;
    }
}
