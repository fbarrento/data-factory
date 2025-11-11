<?php

declare(strict_types=1);

use Tests\Examples\Order\Order;
use Tests\Examples\Order\OrderStatus;

test('creates order with random enum', function (): void {
    /** @var Order $order */
    $order = Order::factory()->make();

    expect($order->status)->toBeInstanceOf(OrderStatus::class)
        ->and($order->id)->toBeString()
        ->and($order->total)->toBeFloat()
        ->and($order->createdAt)->toBeString();
});

test('creates multiple orders with random enums', function (): void {
    /** @var Order[] $orders */
    $orders = Order::factory()->count(10)->make();

    expect($orders)->toHaveCount(10);

    foreach ($orders as $order) {
        expect($order->status)->toBeInstanceOf(OrderStatus::class);
    }
});

test('enum state method works - pending', function (): void {
    /** @var Order $order */
    $order = Order::factory()->pending()->make();

    expect($order->status)->toBe(OrderStatus::Pending)
        ->and($order->status->value)->toBe('pending');
});

test('enum state method works - processing', function (): void {
    /** @var Order $order */
    $order = Order::factory()->processing()->make();

    expect($order->status)->toBe(OrderStatus::Processing);
});

test('enum state method works - shipped', function (): void {
    /** @var Order $order */
    $order = Order::factory()->shipped()->make();

    expect($order->status)->toBe(OrderStatus::Shipped);
});

test('enum state method works - delivered', function (): void {
    /** @var Order $order */
    $order = Order::factory()->delivered()->make();

    expect($order->status)->toBe(OrderStatus::Delivered);
});

test('enum state method works - cancelled', function (): void {
    /** @var Order $order */
    $order = Order::factory()->cancelled()->make();

    expect($order->status)->toBe(OrderStatus::Cancelled);
});

test('sequences work with enums', function (): void {
    /** @var Order[] $orders */
    $orders = Order::factory()
        ->count(4)
        ->sequence(
            ['status' => OrderStatus::Pending],
            ['status' => OrderStatus::Delivered],
        )
        ->make();

    expect($orders[0]->status)->toBe(OrderStatus::Pending)
        ->and($orders[1]->status)->toBe(OrderStatus::Delivered)
        ->and($orders[2]->status)->toBe(OrderStatus::Pending)
        ->and($orders[3]->status)->toBe(OrderStatus::Delivered);
});

test('can override enum in make', function (): void {
    /** @var Order $order */
    $order = Order::factory()->make(['status' => OrderStatus::Cancelled]);

    expect($order->status)->toBe(OrderStatus::Cancelled);
});

test('enum values are properly typed', function (): void {
    /** @var Order $order */
    $order = Order::factory()->pending()->make();

    expect($order->status->value)->toBeString()
        ->and($order->status->value)->toBe('pending')
        ->and($order->status->name)->toBe('Pending');
});

test('all enum cases can be used', function (): void {
    $cases = OrderStatus::cases();

    expect($cases)->toHaveCount(5)
        ->and($cases[0])->toBe(OrderStatus::Pending)
        ->and($cases[1])->toBe(OrderStatus::Processing)
        ->and($cases[2])->toBe(OrderStatus::Shipped)
        ->and($cases[3])->toBe(OrderStatus::Delivered)
        ->and($cases[4])->toBe(OrderStatus::Cancelled);
});
