<?php

namespace Tests\Helpers;

use FBarrento\DataFactory\HasDataFactory;

class Vehicle
{
    /** @use HasDataFactory<VehicleFactory> */
    use HasDataFactory;

    public function __construct(
        public string $make,
        public string $model,
    ) {}

    public static function newFactory(): VehicleFactory
    {
        return new VehicleFactory;
    }
}
