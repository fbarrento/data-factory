<?php

declare(strict_types=1);

use FBarrento\DataFactory\Sequence;
use Tests\Helpers\Vehicle;

test('alternates values with sequence', function (): void {
    /** @var Vehicle[] $vehicles */
    $vehicles = Vehicle::factory()
        ->count(4)
        ->sequence(
            ['make' => 'Mercedes'],
            ['make' => 'BMW'],
        )
        ->make();

    expect($vehicles[0]->make)->toBe('Mercedes')
        ->and($vehicles[1]->make)->toBe('BMW')
        ->and($vehicles[2]->make)->toBe('Mercedes')
        ->and($vehicles[3]->make)->toBe('BMW');
});

test('sequence convenience method works', function (): void {
    /** @var Vehicle[] $vehicles */
    $vehicles = Vehicle::factory()
        ->count(3)
        ->sequence(
            ['make' => 'Ford'],
            ['make' => 'Chevy'],
            ['make' => 'Toyota'],
        )
        ->make();

    expect($vehicles[0]->make)->toBe('Ford')
        ->and($vehicles[1]->make)->toBe('Chevy')
        ->and($vehicles[2]->make)->toBe('Toyota');
});

test('sequence with closure and index', function (): void {
    /** @var Vehicle[] $vehicles */
    $vehicles = Vehicle::factory()
        ->count(3)
        ->sequence(
            fn (Sequence $seq): array => ['model' => 'Model-'.$seq->index]
        )
        ->make();

    expect($vehicles[0]->model)->toBe('Model-0')
        ->and($vehicles[1]->model)->toBe('Model-1')
        ->and($vehicles[2]->model)->toBe('Model-2');
});

test('multiple sequences work together', function (): void {
    /** @var Vehicle[] $vehicles */
    $vehicles = Vehicle::factory()
        ->count(2)
        ->sequence(['make' => 'Ford'], ['make' => 'Chevy'])
        ->sequence(['model' => 'Sedan'], ['model' => 'SUV'])
        ->make();

    expect($vehicles[0]->make)->toBe('Ford')
        ->and($vehicles[0]->model)->toBe('Sedan')
        ->and($vehicles[1]->make)->toBe('Chevy')
        ->and($vehicles[1]->model)->toBe('SUV');
});

test('sequence with single count uses first value', function (): void {
    /** @var Vehicle $vehicle */
    $vehicle = Vehicle::factory()
        ->sequence(['make' => 'Mercedes'], ['make' => 'BMW'])
        ->make();

    expect($vehicle->make)->toBe('Mercedes');
});

test('sequences reset between make calls', function (): void {
    $factory = Vehicle::factory()
        ->sequence(['make' => 'A'], ['make' => 'B']);

    /** @var Vehicle[] $batch1 */
    $batch1 = $factory->count(2)->make();
    /** @var Vehicle[] $batch2 */
    $batch2 = $factory->count(2)->make();

    expect($batch1[0]->make)->toBe('A')
        ->and($batch1[1]->make)->toBe('B')
        ->and($batch2[0]->make)->toBe('A') // Resets!
        ->and($batch2[1]->make)->toBe('B');
});

test('count exceeds sequence length wraps around', function (): void {
    /** @var Vehicle[] $vehicles */
    $vehicles = Vehicle::factory()
        ->count(5)
        ->sequence(['make' => 'A'], ['make' => 'B'])
        ->make();

    expect($vehicles[0]->make)->toBe('A')
        ->and($vehicles[1]->make)->toBe('B')
        ->and($vehicles[2]->make)->toBe('A')
        ->and($vehicles[3]->make)->toBe('B')
        ->and($vehicles[4]->make)->toBe('A');
});

test('sequence works with custom state method', function (): void {
    /** @var Vehicle[] $vehicles */
    $vehicles = Vehicle::factory()
        ->count(2)
        ->mercedes()
        ->sequence(['model' => 'A-Class'], ['model' => 'C-Class'])
        ->make();

    expect($vehicles[0]->make)->toBe('Mercedes')
        ->and($vehicles[0]->model)->toBe('A-Class')
        ->and($vehicles[1]->make)->toBe('Mercedes')
        ->and($vehicles[1]->model)->toBe('C-Class');
});

test('sequence with make attributes', function (): void {
    /** @var Vehicle[] $vehicles */
    $vehicles = Vehicle::factory()
        ->count(2)
        ->sequence(['make' => 'A'], ['make' => 'B'])
        ->make(['model' => 'Override']);

    expect($vehicles[0]->make)->toBe('A')
        ->and($vehicles[0]->model)->toBe('Override')
        ->and($vehicles[1]->make)->toBe('B')
        ->and($vehicles[1]->model)->toBe('Override');
});

test('sequence integrates with definition', function (): void {
    /** @var Vehicle[] $vehicles */
    $vehicles = Vehicle::factory()
        ->count(2)
        ->sequence(['make' => 'Custom'])
        ->make();

    // Definition provides 'model', sequence provides 'make'
    expect($vehicles[0]->make)->toBe('Custom')
        ->and($vehicles[0]->model)->toBeString()
        ->and($vehicles[1]->make)->toBe('Custom');
});

test('sequences work with mercedes state method', function (): void {
    /** @var Vehicle[] $vehicles */
    $vehicles = Vehicle::factory()
        ->count(2)
        ->sequence(['model' => 'S-Class'], ['model' => 'E-Class'])
        ->mercedes()
        ->make();

    expect($vehicles[0]->make)->toBe('Mercedes')
        ->and($vehicles[0]->model)->toBe('S-Class')
        ->and($vehicles[1]->make)->toBe('Mercedes')
        ->and($vehicles[1]->model)->toBe('E-Class');
});

test('sequence with non-array scalar value gets ignored', function (): void {
    /** @var Vehicle[] $vehicles */
    $vehicles = Vehicle::factory()
        ->count(2)
        ->sequence('scalar1', 'scalar2')
        ->make();

    // Non-array sequence values are ignored for object construction
    expect($vehicles)->toBeArray()->toHaveCount(2);
});
