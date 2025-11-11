<?php

declare(strict_types=1);

use FBarrento\DataFactory\ArrayFactory;
use Tests\Examples\Vehicle\VehicleArrayFactory;

dataset('vehicles', [
    [fn () => VehicleArrayFactory::new()->make(['make' => 'Toyota'])],
    [fn () => VehicleArrayFactory::new()->make(['make' => 'Toyota'])],
]);

// @phpstan-ignore-next-line method.notFound
test('can be used with data sets', function (array $vehicle): void {

    expect($vehicle['make'])->toBe('Toyota');

})->with('vehicles');

test('creates an array from the array factory', function (): void {

    $vehicleArray = VehicleArrayFactory::new()
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
    $arrayFactory = VehicleArrayFactory::new();

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
