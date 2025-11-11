<?php

use Tests\Helpers\Vehicle;
use Tests\Helpers\VehicleFactory;

test('makes a mercedes', function (): void {

    $vehicleFactory = new VehicleFactory;

    /** @var Vehicle[] $vehicles */
    $vehicles = $vehicleFactory
        ->count(10)
        ->mercedes()
        ->make();

    expect($vehicles)
        ->toBeArray()
        ->and($vehicles[0]->make)
        ->toBe('Mercedes');

});

test('makes multiple objects', function (): void {

    $vehicleFactory = new VehicleFactory;

    /** @var array<int, Vehicle> $vehicles */
    $vehicles = $vehicleFactory->count(3)->make();

    expect($vehicles)
        ->toBeArray()
        ->toHaveCount(3);

    foreach ($vehicles as $vehicle) {
        expect($vehicle)
            ->toBeInstanceOf(Vehicle::class);
    }

});

test('makes the object', function (): void {

    $vehicleFactory = new VehicleFactory;

    $vehicle = $vehicleFactory->make();

    expect($vehicle)
        ->toBeInstanceOf(Vehicle::class)
        ->and($vehicle)->not->toBeArray();

});

test('state method with array works through custom method', function (): void {

    /** @var Vehicle $vehicle */
    $vehicle = Vehicle::factory()
        ->withModel('Corolla')
        ->make();

    expect($vehicle->model)->toBe('Corolla');

});
