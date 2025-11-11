<?php

namespace FBarrento\DataFactory\Concerns;

use FBarrento\DataFactory\Factory;

/**
 * @template TFactory of Factory
 */
trait HasDataFactory
{
    /**
     * @return TFactory
     */
    public static function factory(): mixed
    {
        /** @var TFactory */
        return static::newFactory();
    }
}
