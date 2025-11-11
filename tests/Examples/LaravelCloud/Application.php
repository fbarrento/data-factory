<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

use DateTimeInterface;
use FBarrento\DataFactory\HasDataFactory;

readonly class Application
{
    /** @use HasDataFactory<ApplicationFactory> */
    use HasDataFactory;

    /**
     * @param  array<int, Environment>  $environments
     * @param  array<int, Deployment>  $deployments
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $region,
        public Repository $repository,
        public Organization $organization,
        public ?Environment $defaultEnvironment = null,
        public array $environments = [],
        public array $deployments = [],
        public DateTimeInterface $createdAt = new \DateTime,
    ) {}

    public static function newFactory(): ApplicationFactory
    {
        return new ApplicationFactory;
    }
}
