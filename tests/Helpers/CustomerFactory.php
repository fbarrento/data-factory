<?php

namespace Tests\Helpers;

use FBarrento\DataFactory\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected string $dataObject = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->fake->name(),
            'email' => $this->fake->email(),
            'address' => Address::factory(),
        ];
    }

    public function withLondonAddress(): self
    {
        return $this->state(fn (array $attributes): array => [
            'address' => Address::factory()->london(),
        ]);
    }
}
