<?php

declare(strict_types=1);

use FBarrento\DataFactory\Sequence;

test('cycles through values', function (): void {
    $seq = new Sequence('A', 'B', 'C');

    expect($seq())->toBe('A')
        ->and($seq())->toBe('B')
        ->and($seq())->toBe('C')
        ->and($seq())->toBe('A'); // Wraps around
});

test('tracks index', function (): void {
    $seq = new Sequence('A', 'B');

    expect($seq->index)->toBe(0);
    $seq();
    expect($seq->index)->toBe(1);
    $seq();
    expect($seq->index)->toBe(2);
});

test('resolves closures with sequence instance', function (): void {
    $seq = new Sequence(
        fn (Sequence $s): string => 'Value-'.$s->index
    );

    expect($seq())->toBe('Value-0')
        ->and($seq())->toBe('Value-1');
});

test('is countable', function (): void {
    $seq = new Sequence('A', 'B', 'C');

    expect($seq)->toHaveCount(3);
});

// @phpstan-ignore-next-line method.notFound
test('throws on empty sequence', function (): void {
    new Sequence;
})->throws(\InvalidArgumentException::class, 'Sequence must contain at least one value');

test('handles array values', function (): void {
    $seq = new Sequence(
        ['key' => 'value1'],
        ['key' => 'value2']
    );

    expect($seq())->toBe(['key' => 'value1'])
        ->and($seq())->toBe(['key' => 'value2'])
        ->and($seq())->toBe(['key' => 'value1']);
});

test('handles mixed types', function (): void {
    $seq = new Sequence('string', 123, true, ['array']);

    expect($seq())->toBe('string')
        ->and($seq())->toBe(123)
        ->and($seq())->toBe(true)
        ->and($seq())->toBe(['array'])
        ->and($seq())->toBe('string'); // Wraps
});

test('closure receives sequence instance', function (): void {
    $receivedInstance = null;

    $seq = new Sequence(
        function (Sequence $s) use (&$receivedInstance): string {
            $receivedInstance = $s;

            return 'test';
        }
    );

    $seq();

    expect($receivedInstance)->toBeInstanceOf(Sequence::class)
        ->and($receivedInstance)->toBe($seq);
});

test('index increments on each invocation', function (): void {
    $seq = new Sequence('A');

    expect($seq->index)->toBe(0);
    $seq();
    expect($seq->index)->toBe(1);
    $seq();
    expect($seq->index)->toBe(2);
    $seq();
    expect($seq->index)->toBe(3);
});
