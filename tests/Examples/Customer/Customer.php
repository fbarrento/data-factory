<?php

namespace Tests\Examples\Customer;

use FBarrento\DataFactory\HasDataFactory;

class Customer
{
    /** @use HasDataFactory<CustomerFactory> */
    use HasDataFactory;

    public function __construct(
        public string $name,
        public string $email,
        public Address $address,
    ) {}

    public static function newFactory(): CustomerFactory
    {
        return new CustomerFactory;
    }
}
