<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

use FBarrento\DataFactory\Concerns\HasDataFactory;

readonly class Repository
{
    /** @use HasDataFactory<RepositoryFactory> */
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $name,
        public string $fullName,
        public string $defaultBranch,
    ) {}

    public static function newFactory(): RepositoryFactory
    {
        return new RepositoryFactory;
    }
}
