<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

use FBarrento\DataFactory\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    protected string $dataObject = Organization::class;

    public function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
        ];
    }
}
