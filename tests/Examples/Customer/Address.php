<?php

namespace Tests\Examples\Customer;

use FBarrento\DataFactory\Concerns\HasDataFactory;

class Address
{
    /** @use HasDataFactory<AddressFactory> */
    use HasDataFactory;

    public function __construct(
        public string $street,
        public string $city,
        public string $zipCode,
    ) {}

    public static function newFactory(): AddressFactory
    {
        return new AddressFactory;
    }
}
