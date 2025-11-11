<?php

namespace Tests\Examples\Vehicle;

use FBarrento\DataFactory\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected string $dataObject = Vehicle::class;

    public function definition(): array
    {
        return [
            'make' => $this->fake->company(),
            'model' => $this->fake->word(),
        ];
    }

    public function mercedes(): self
    {
        return $this->state(fn (array $attributes): array => [
            'make' => 'Mercedes',
        ]);
    }

    public function withModel(string $model): self
    {
        return $this->state(['model' => $model]);
    }
}
