<?php

declare(strict_types=1);

namespace Tests\Examples\Order;

use FBarrento\DataFactory\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected string $dataObject = Order::class;

    public function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'status' => $this->fake->randomElement(OrderStatus::cases()),
            'total' => $this->fake->randomFloat(2, 10, 1000),
            'createdAt' => $this->fake->dateTime()->format('Y-m-d H:i:s'),
        ];
    }

    public function pending(): self
    {
        return $this->state(['status' => OrderStatus::Pending]);
    }

    public function processing(): self
    {
        return $this->state(['status' => OrderStatus::Processing]);
    }

    public function shipped(): self
    {
        return $this->state(['status' => OrderStatus::Shipped]);
    }

    public function delivered(): self
    {
        return $this->state(['status' => OrderStatus::Delivered]);
    }

    public function cancelled(): self
    {
        return $this->state(['status' => OrderStatus::Cancelled]);
    }
}
