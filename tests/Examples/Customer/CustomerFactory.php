<?php

namespace Tests\Examples\Customer;

use FBarrento\DataFactory\Factory;
use Tests\Examples\Order\Order;

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
            'orders' => function (): array {
                /** @var array<int, Order> */
                $orders = Order::factory()->count(3)->make();

                return $orders;
            },
        ];
    }

    public function withLondonAddress(): self
    {
        return $this->state(fn (array $attributes): array => [
            'address' => Address::factory()->london(),
        ]);
    }
}
