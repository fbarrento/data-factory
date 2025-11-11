<?php

declare(strict_types=1);

use FBarrento\DataFactory\ArrayFactory;
use Tests\Examples\Customer\Address;
use Tests\Examples\Customer\Customer;
use Tests\Examples\Order\Order;
use Tests\Examples\Order\OrderStatus;

test('creates customer with nested address using static factory method', function (): void {

    /** @var Customer $customer */
    $customer = Customer::factory()->make();

    expect($customer)
        ->toBeInstanceOf(Customer::class)
        ->and($customer->name)->toBeString()
        ->and($customer->email)->toBeString()
        ->and($customer->address)->toBeInstanceOf(Address::class)
        ->and($customer->address->street)->toBeString()
        ->and($customer->address->city)->toBeString()
        ->and($customer->address->zipCode)->toBeString();

});

test('creates customer with nested address using direct factory instantiation', function (): void {

    /** @var Customer $customer */
    $customer = Customer::factory()
        ->make();

    expect($customer)
        ->toBeInstanceOf(Customer::class)
        ->and($customer->address)->toBeInstanceOf(Address::class);

});

test('creates customer with nested address using closure syntax', function (): void {

    /** @var Customer $customer */
    $customer = Customer::factory()
        ->withLondonAddress()
        ->make();

    expect($customer)
        ->toBeInstanceOf(Customer::class)
        ->and($customer->address)->toBeInstanceOf(Address::class)
        ->and($customer->address->city)->toBe('London');

});

test('each make call creates fresh nested objects', function (): void {

    $factory = Customer::factory();

    /** @var Customer $customer1 */
    $customer1 = $factory->make();
    /** @var Customer $customer2 */
    $customer2 = $factory->make();

    // Each call should create a different Address instance
    expect($customer1->address)
        ->toBeInstanceOf(Address::class)
        ->and($customer2->address)->toBeInstanceOf(Address::class)
        ->and($customer1->address)->not->toBe($customer2->address)
        ->and(spl_object_id($customer1->address))
        ->not->toBe(spl_object_id($customer2->address));

});

test('can override nested factory with custom attributes', function (): void {

    $customAddress = new Address(
        street: '123 Custom St',
        city: 'Custom City',
        zipCode: '12345'
    );

    /** @var Customer $customer */
    $customer = Customer::factory()
        ->make([
            'name' => 'John Doe',
            'address' => $customAddress,
        ]);

    expect($customer->name)->toBe('John Doe')
        ->and($customer->address)->toBe($customAddress)
        ->and($customer->address->street)->toBe('123 Custom St');

});

test('can use state method with nested factory', function (): void {

    /** @var Customer $customer */
    $customer = Customer::factory()
        ->withLondonAddress()
        ->make();

    expect($customer->address->city)->toBe('London');

});

test('nested factory works with count method', function (): void {

    /** @var Customer[] $customers */
    $customers = Customer::factory()
        ->count(3)
        ->make();

    expect($customers)->toBeArray()
        ->toHaveCount(3);

    foreach ($customers as $customer) {
        expect($customer)
            ->toBeInstanceOf(Customer::class)
            ->and($customer->address)->toBeInstanceOf(Address::class);
    }

    // Verify each customer has a different address instance
    expect($customers[0]->address)->not->toBe($customers[1]->address)
        ->and($customers[1]->address)->not->toBe($customers[2]->address);

});

test('nested factory in array factory works with direct factory instance', function (): void {

    $customerArray = new class extends ArrayFactory
    {
        public function definition(): array
        {
            return [
                'name' => $this->fake->name(),
                'email' => $this->fake->email(),
                'address' => Address::factory(),
            ];
        }
    };

    $result = $customerArray->make();

    expect($result)
        ->toBeArray()
        ->toHaveKey('name')
        ->toHaveKey('email')
        ->toHaveKey('address')
        ->and($result['address'])->toBeInstanceOf(Address::class);

});

test('nested factory in array factory works with closure', function (): void {

    $customerArray = new class extends ArrayFactory
    {
        public function definition(): array
        {
            return [
                'name' => $this->fake->name(),
                'email' => $this->fake->email(),
                'address' => fn () => Address::factory()->make(),
            ];
        }
    };

    $result = $customerArray->make();

    expect($result)
        ->toBeArray()
        ->toHaveKey('address')
        ->and($result['address'])->toBeInstanceOf(Address::class);

});

test('can create address standalone', function (): void {

    /** @var Address $address */
    $address = Address::factory()->make();

    expect($address)
        ->toBeInstanceOf(Address::class)
        ->and($address->street)->toBeString()
        ->and($address->city)->toBeString()
        ->and($address->zipCode)->toBeString();

});

test('address factory london state method works', function (): void {

    /** @var Address $address */
    $address = Address::factory()
        ->london()
        ->make();

    expect($address->city)->toBe('London');

});

test('customer has nested orders with enums', function (): void {

    /** @var Customer $customer */
    $customer = Customer::factory()->make();

    expect($customer->orders)->toBeArray()
        ->and($customer->orders)->toHaveCount(3);

    foreach ($customer->orders as $order) {
        expect($order)
            ->toBeInstanceOf(Order::class)
            ->and($order->status)->toBeInstanceOf(OrderStatus::class);
    }

});

test('can create customer with specific number of orders', function (): void {

    /** @var Customer $customer */
    $customer = Customer::factory()
        ->make([
            'orders' => Order::factory()->count(5)->make(),
        ]);

    expect($customer->orders)->toHaveCount(5);

    foreach ($customer->orders as $order) {
        expect($order)->toBeInstanceOf(Order::class);
    }

});

test('can create customer with delivered orders', function (): void {

    /** @var Customer $customer */
    $customer = Customer::factory()
        ->make([
            'orders' => Order::factory()->delivered()->count(2)->make(),
        ]);

    expect($customer->orders)->toHaveCount(2);

    foreach ($customer->orders as $order) {
        expect($order->status)->toBe(OrderStatus::Delivered);
    }

});

test('multiple customers have different order instances', function (): void {

    /** @var Customer[] $customers */
    $customers = Customer::factory()->count(2)->make();

    expect($customers)->toHaveCount(2)
        ->and($customers[0]->orders)->toBeArray()
        ->and($customers[1]->orders)->toBeArray();

    // Verify orders are different instances
    expect($customers[0]->orders[0])
        ->not->toBe($customers[1]->orders[0]);

});
