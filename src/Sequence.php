<?php

declare(strict_types=1);

namespace FBarrento\DataFactory;

use Closure;
use Countable;
use InvalidArgumentException;

class Sequence implements Countable
{
    /**
     * @var array<int, mixed>
     */
    protected array $sequence;

    /**
     * @var int<0, max>
     */
    public int $count;

    public int $index = 0;

    /**
     * @param  mixed  ...$sequence
     */
    public function __construct(...$sequence)
    {
        if ($sequence === []) {
            throw new InvalidArgumentException('Sequence must contain at least one value');
        }

        $this->sequence = array_values($sequence);
        $this->count = count($sequence);
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * Get the next value in the sequence.
     *
     * @return array<string, mixed>|mixed
     */
    public function __invoke(): mixed
    {
        $value = $this->sequence[$this->index % $this->count];

        // Resolve closures, passing the sequence instance
        if ($value instanceof Closure) {
            $result = $value($this);
            $this->index++;

            return $result;
        }

        $this->index++;

        return $value;
    }
}
