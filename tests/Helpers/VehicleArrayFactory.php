<?php

namespace Tests\Helpers;

use FBarrento\DataFactory\ArrayFactory;

/**
 * @extends ArrayFactory<array{make:string, model:string}>
 */
class VehicleArrayFactory extends ArrayFactory
{
    public function definition(): array
    {
        return [
            'make' => $this->fake->company,
            'model' => $this->fake->word,
        ];
    }

    public function mercedes(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'make' => 'Mercedes',
            ];
        });
    }
}
