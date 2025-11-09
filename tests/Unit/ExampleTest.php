<?php

test('that true is true', function (): void {
    expect(true)->toBeTrue();
});

test('example returns bar', function (): void {
    $example = new NunoMaduro\SkeletonPhp\Example;

    expect($example->foo())->toBe('bar');
});
