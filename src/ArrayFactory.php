<?php

declare(strict_types=1);

namespace FBarrento\DataFactory;

/**
 * @template TArray of array<string, mixed>
 *
 * @template-extends Factory<TArray>
 */
abstract class ArrayFactory extends Factory
{
    public static function new(): static
    {
        return new static; // @phpstan-ignore-line
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return TArray
     */
    public function make(array $attributes = []): mixed
    {
        $result = parent::make($attributes);

        /** @var TArray $result */
        return $result;
    }

    /**
     * @return TArray
     */
    protected function makeInstance(): mixed
    {

        /** @var TArray $state */
        $state = $this->state;

        return $state;

    }
}
