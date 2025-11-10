<?php

declare(strict_types=1);

namespace FBarrento\DataFactory;

use Closure;
use Faker\Factory as Faker;
use Faker\Generator;

/**
 * @template TDataObject of object|array
 */
abstract class Factory
{
    /**
     * @var class-string
     */
    protected string $dataObject;

    /**
     * @var array<string, mixed>
     */
    protected array $state;

    protected Generator $fake;

    private int $count = 1;

    /**
     * @var array<int, TDataObject>
     */
    private array $instances = [];

    public function __construct()
    {
        $this->fake = Faker::create();
        $this->state = $this->definition();
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return TDataObject|array<int, TDataObject>
     */
    public function make(array $attributes = []): mixed
    {
        $this->state = [
            ...$this->definition(),
            ...$this->state,
        ];

        if ($attributes !== []) {
            $this->state = [
                ...$this->state,
                ...$attributes,
            ];
        }

        if ($this->count === 1) {
            return $this->makeInstance();
        }

        foreach (range(0, $this->count - 1) as $i) {
            $this->instances[] = $this->makeInstance();
        }

        return $this->instances;

    }

    /**
     * @return $this
     */
    public function count(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return TDataObject
     */
    protected function makeInstance(): mixed
    {
        $resolved = $this->resolveNestedFactories($this->state);

        return new $this->dataObject(...$resolved); // @phpstan-ignore-line
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    protected function resolveNestedFactories(array $state): array
    {

        return array_map(fn ($value) => match (true) {
            $value instanceof Closure => $value(),
            $value instanceof Factory => (clone $value)->make(),
            default => $value,
        }, $state);
    }

    /**
     * @param  Closure(array<string, mixed>): array<string, mixed>  $state
     * @return $this
     */
    protected function state(Closure $state): self
    {
        $this->state = [
            ...$this->state,
            ...$state($this->state),
        ];

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function definition(): array;
}
