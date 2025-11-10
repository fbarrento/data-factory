<?php

declare(strict_types=1);

use FBarrento\DataFactory\ArrayFactory;
use Tests\Helpers\VehicleArrayFactory;

dataset('vehicles', [
    [fn () => new VehicleArrayFactory()->make(['make' => 'Toyota'])],
    [fn () => new VehicleArrayFactory()->make(['make' => 'Toyota'])],
]);

test('can be used with data sets', function (array $vehicle): void {

    expect($vehicle['make'])->toBe('Toyota');

})->with('vehicles');

test('creates an array from the array factory', function (): void {

    $vehicleArray = new VehicleArrayFactory()
        ->make([
            'make' => 'Toyota',
            'model' => 'Corolla',
        ]);

    expect($vehicleArray)
        ->toBeArray()
        ->and($vehicleArray)->toBe([
        'make' => 'Toyota',
        'model' => 'Corolla',
    ]);

});

test('can create a array factory', function (): void {
    $arrayFactory = new VehicleArrayFactory;

    expect($arrayFactory)
        ->toBeInstanceOf(ArrayFactory::class);
});

test('can create an array factory using static new method', function (): void {
    $vehicleArray = VehicleArrayFactory::new()
        ->make([
            'make' => 'Honda',
            'model' => 'Civic',
        ]);

    expect($vehicleArray)
        ->toBeArray()
        ->and($vehicleArray)->toBe([
        'make' => 'Honda',
        'model' => 'Civic',
    ]);
});
