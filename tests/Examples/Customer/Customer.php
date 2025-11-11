<?php

namespace Tests\Examples\Customer;

use FBarrento\DataFactory\HasDataFactory;

class Customer
{
    /** @use HasDataFactory<CustomerFactory> */
    use HasDataFactory;

    /**
     * @param  array<int, \Tests\Examples\Order\Order>  $orders
     */
    public function __construct(
        public string $name,
        public string $email,
        public Address $address,
        public array $orders = [],
    ) {}

    public static function newFactory(): CustomerFactory
    {
        return new CustomerFactory;
    }
}
