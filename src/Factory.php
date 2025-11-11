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
            $result = $this->makeInstance();
            $this->resetSequences();

            return $result;
        }

        foreach (range(0, $this->count - 1) as $i) {
            $this->instances[] = $this->makeInstance();
        }

        $this->resetSequences();

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
        // Resolve sequences first
        $state = $this->resolveSequences($this->state);

        // Then resolve nested factories
        $resolved = $this->resolveNestedFactories($state);

        return new $this->dataObject(...$resolved); // @phpstan-ignore-line
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    protected function resolveNestedFactories(array $state): array
    {

        return array_map(fn (mixed $value): mixed => match (true) {
            $value instanceof Closure => $value(),
            $value instanceof Factory => (clone $value)->make(),
            default => $value,
        }, $state);
    }

    /**
     * Resolve any Sequence objects in the state.
     *
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    protected function resolveSequences(array $state): array
    {
        /** @var array<string, mixed> $resolved */
        $resolved = [];

        foreach ($state as $key => $value) {
            // If it's a sequence, invoke it to get the next value
            if ($value instanceof Sequence) {
                $sequenceValue = $value();

                // Merge array results, skip non-array (scalar sequences not supported for objects)
                if (is_array($sequenceValue)) {
                    /** @var array<string, mixed> $sequenceValue */
                    $resolved = [...$resolved, ...$sequenceValue];
                }
                // Scalar sequence values are ignored for object construction
            } else {
                $resolved[$key] = $value;
            }
        }

        return $resolved;
    }

    /**
     * Reset sequence indices after make() completes.
     */
    protected function resetSequences(): void
    {
        foreach ($this->state as $value) {
            if ($value instanceof Sequence) {
                $value->index = 0;
            }
        }
    }

    /**
     * @param  Closure(array<string, mixed>): array<string, mixed>|Sequence|array<string, mixed>  $state
     * @return $this
     */
    protected function state(Closure|Sequence|array $state): self
    {
        if ($state instanceof Sequence) {
            // Store a sequence with a unique key to allow multiple sequences
            $this->state['__sequence_'.uniqid()] = $state;

            return $this;
        }

        if (is_array($state)) {
            $this->state = [...$this->state, ...$state];

            return $this;
        }

        // Existing closure behavior
        $this->state = [
            ...$this->state,
            ...$state($this->state),
        ];

        return $this;
    }

    /**
     * Add a sequence of states to apply to multiple instances.
     *
     * @param  mixed  ...$sequence
     * @return $this
     */
    public function sequence(...$sequence): self
    {
        return $this->state(new Sequence(...$sequence));
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function definition(): array;
}
