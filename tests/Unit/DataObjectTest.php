<?php

use Tests\Helpers\Vehicle;

test('factory method creates a instance', function (): void {

    /** @var Vehicle $vehicle */
    $vehicle = Vehicle::factory()
        ->mercedes()
        ->make();

    expect($vehicle)
        ->toBeInstanceOf(Vehicle::class)
        ->and($vehicle->make)
        ->toBe('Mercedes');

});
