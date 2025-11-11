<?php

declare(strict_types=1);

namespace Tests\Examples\Order;

use FBarrento\DataFactory\HasDataFactory;

readonly class Order
{
    /** @use HasDataFactory<OrderFactory> */
    use HasDataFactory;

    public function __construct(
        public string $id,
        public OrderStatus $status,
        public float $total,
        public string $createdAt,
    ) {}

    public static function factory(): OrderFactory
    {
        return new OrderFactory;
    }
}
