# PHP Data Factory


<p align="center">
    <a href="https://github.com/fbarrento/data-factory/actions"><img alt="GitHub Workflow Status" src="https://github.com/fbarrento/data-factory/actions/workflows/tests.yml/badge.svg"></a>
    <a href="https://packagist.org/packages/fbarrento/data-factory"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/fbarrento/data-factory"></a>
    <a href="https://packagist.org/packages/fbarrento/data-factory"><img alt="Latest Version" src="https://img.shields.io/packagist/v/fbarrento/data-factory"></a>
    <a href="https://packagist.org/packages/fbarrento/data-factory"><img alt="License" src="https://img.shields.io/packagist/l/fbarrento/data-factory"></a>
</p>

------

## Stop Writing Arrays in Your Tests: Use Array and Data Object Factories

**The modern test data factory for PEST.** Write cleaner, more maintainable PHP tests with expressive factories.

Inspired by Laravel's Eloquent factories, Data Factory brings the same elegant API to any PHP project—no framework required.

> **Requires [PHP 8.2+](https://php.net/releases/)** | Fully tested on PHP 8.2, 8.3, 8.4 across Windows, Linux, and macOS

## Features

- ✅ **Write DRY test code** - Define test data once, reuse everywhere
- ✅ **Readable test assertions** - Clear intent with named states like `->succeeded()`
- ✅ **Easy complex objects** - Create nested graphs without boilerplate
- ✅ **Built with PEST in mind** - Works with any PHP testing framework, optimized for PEST
- ✅ **Framework-agnostic** - Works with any PHP class, not tied to Eloquent
- ✅ **Type-safe factories** - Modern type hints with 100% type coverage
- ✅ **Faker integration** - Realistic test data out of the box

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require fbarrento/data-factory --dev
```

## Why Use Data Factories for Testing?

**The Problem:** Test setup code is repetitive, hard to maintain, and clutters your test files.

```php
// ❌ Without factories - repetitive and brittle
it('processes deployment', function () {
    $deployment = [
        'id' => '123e4567-e89b-12d3-a456-426614174000',
        'status' => 'deployment.succeeded',
        'branch_name' => 'main',
        'commit_hash' => 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0',
        'commit_message' => 'Deploy feature X to production',
        'failure_reason' => null,
        'php_major_version' => '8.4',
        'uses_octane' => true,
        'started_at' => '2024-01-15 10:00:00',
        'finished_at' => '2024-01-15 10:05:00',
    ];

    // Your actual test logic here...
});
```

```php
// ✅ With factories - clean and focused
it('processes deployment', function () {
    $deployment = Deployment::factory()->succeeded()->make();

    // Your actual test logic here - clear and focused!
});
```

**The Benefits:**

- **DRY (Don't Repeat Yourself)** - Define test data once, reuse across all tests
- **Maintainability** - Change data structure in one place, not hundreds of tests
- **Readability** - `->succeeded()` is clearer than 10 lines of setup
- **Flexibility** - Easy to test edge cases with different states
- **Focus** - Spend time testing behavior, not setting up data

## Quick Start

```php
// tests/Factories/DeploymentFactory.php
use FBarrento\DataFactory\Factory;

class DeploymentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'status' => 'pending',
            'branch_name' => 'main',
            'commit_hash' => $this->fake->sha1(),
        ];
    }

    public function succeeded(): static
    {
        return $this->state(['status' => 'deployment.succeeded']);
    }
}

// tests/Feature/DeploymentTest.php
it('handles successful deployments', function () {
    $deployment = Deployment::factory()->succeeded()->make();

    expect($deployment->status)->toBe('deployment.succeeded');
});

it('creates multiple test deployments', function () {
    $deployments = Deployment::factory()->count(5)->make();

    expect($deployments)->toHaveCount(5);
});
```

## Documentation

- **[Getting Started](docs/index.md)** - Overview and feature list
- **[Installation](docs/installation.md)** - Install and setup
- **[Basic Usage](docs/basic-usage.md)** - Create your first factory
- **[Faker Integration](docs/faker.md)** - Generate realistic fake data
- **[States](docs/states.md)** - Define reusable variations
- **[Sequences](docs/sequences.md)** - Cycle through values
- **[Nested Factories](docs/nested-factories.md)** - Build complex object graphs
- **[Array Factories](docs/array-factories.md)** - Generate arrays for JSON/API responses
- **[Class Integration](docs/model-integration.md)** - Integrate factories with your classes using HasDataFactory trait
- **[Advanced Examples](docs/advanced-examples.md)** - Real-world Laravel Cloud API examples
- **[Testing](docs/testing.md)** - Use factories in PEST tests

## Roadmap

Features under consideration for future releases:

- 🎯 **`raw()` method** - Return attribute arrays without instantiating objects, useful for testing raw data or API payloads
  ```php
  $attributes = Vehicle::factory()->raw(); // Returns ['make' => '...', 'model' => '...']
  ```
- 🪝 **`afterMaking()` callbacks** - Post-processing hooks for generated data, enabling validation or side effects
  ```php
  Vehicle::factory()
      ->afterMaking(fn($vehicle) => logger()->info('Made vehicle', ['id' => $vehicle->id]))
      ->make();
  ```
- 🗂️ **Export to JSON** - Save datasets as JSON fixtures for testing or seeding
  ```php
  Vehicle::factory()->count(1000)->toJson('fixtures/vehicles.json');
  ```
- 📦 **Export to Arrays** - Transform objects to nested array structures
  ```php
  $array = Customer::factory()->count(100)->toArray();
  ```
- ⚡ **Streaming/Chunking** - Process large datasets (100k+ records) without memory issues
  ```php
  Vehicle::factory()->count(200000)->chunk(1000)->toJson('fixtures/vehicles.json');
  ```
- 🚨 **Exception Handling** - Custom exceptions with helpful error messages for configuration and validation errors

Have ideas for other features? [Open an issue](https://github.com/fbarrento/data-factory/issues) or submit a PR!

## Contributing

We welcome contributions! Data Factory is built with high quality standards:

- ✅ 100% test coverage
- ✅ 100% type coverage
- ✅ PHPStan level 9
- ✅ Modern PHP 8.2+ patterns

**Quick Start for Contributors:**

```bash
# Run all quality checks
composer test

# Format code
composer lint

# Run tests with coverage
composer test:unit
```

For detailed contributing guidelines, development setup, and code standards, see [CONTRIBUTING.md](CONTRIBUTING.md).

## References

- [Stop Writing Arrays in Your Tests: Laravel Factories for Data Objects](https://barrento.test/blog/stop-writing-arrays-in-your-tests-laravel-factories-for-data-objects) - The article that inspired this package

## License

Data Factory is open-sourced software licensed under the [MIT license](LICENSE.md).
