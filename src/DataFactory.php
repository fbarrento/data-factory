<?php

declare(strict_types=1);

namespace FBarrento\DataFactories;

/**
 * @template TDataObject of object
 */
class DataFactory
{
    /**
     * @var class-string<TDataObject>
     */
    protected mixed $dataObject;

    /**
     * @return TDataObject
     */
    public function make(): mixed {}
}
