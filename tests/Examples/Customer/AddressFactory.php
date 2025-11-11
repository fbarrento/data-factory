<?php

namespace Tests\Examples\Customer;

use FBarrento\DataFactory\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected string $dataObject = Address::class;

    public function definition(): array
    {
        return [
            'street' => $this->fake->streetAddress(),
            'city' => $this->fake->city(),
            'zipCode' => $this->fake->postcode(),
        ];
    }

    public function london(): self
    {
        return $this->state(fn (array $attributes): array => [
            'city' => 'London',
        ]);
    }
}
