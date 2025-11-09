<?php

test('that true is true', function (): void {
    expect(true)->toBeTrue();
});

test('example returns bar', function (): void {
    $example = new FBarrento\DataFactory\Example;

    expect($example->foo())->toBe('bar');
});
