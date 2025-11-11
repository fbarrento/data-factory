<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

use FBarrento\DataFactory\Concerns\HasDataFactory;

readonly class Organization
{
    /** @use HasDataFactory<OrganizationFactory> */
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $name,
    ) {}

    public static function newFactory(): OrganizationFactory
    {
        return new OrganizationFactory;
    }
}
