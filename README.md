# Data Factory

<p align="center">
    <a href="https://github.com/fbarrento/data-factory/actions"><img alt="GitHub Workflow Status" src="https://github.com/fbarrento/data-factory/actions/workflows/tests.yml/badge.svg"></a>
    <a href="https://packagist.org/packages/fbarrento/data-factory"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/fbarrento/data-factory"></a>
    <a href="https://packagist.org/packages/fbarrento/data-factory"><img alt="Latest Version" src="https://img.shields.io/packagist/v/fbarrento/data-factory"></a>
    <a href="https://packagist.org/packages/fbarrento/data-factory"><img alt="License" src="https://img.shields.io/packagist/l/fbarrento/data-factory"></a>
</p>

------
A powerful, type-safe **test data factory** library for PHP. Create flexible test data for any PHP class or array structure.

> **Requires [PHP 8.4+](https://php.net/releases/)**

## Features

- ✅ **Framework-agnostic** - Works with any PHP project, not tied to Laravel or Eloquent
- ✅ **Type-safe** - Full PHP 8.4+ generics with 100% type coverage
- ✅ **Flexible data** - Create objects or arrays, perfect for API testing
- ✅ **Faker integration** - Generate realistic fake data out of the box
- ✅ **States & sequences** - Define reusable variations and cycle through values
- ✅ **Nested factories** - Build complex object graphs easily
- ✅ **Clean syntax** - Chainable, intuitive API

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require fbarrento/data-factory --dev
```

## Quick Start

```php
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

// Create a single deployment
$deployment = DeploymentFactory::new()->make();

// Create 5 successful deployments
$deployments = DeploymentFactory::new()->succeeded()->count(5)->make();
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
- **[Model Integration](docs/model-integration.md)** - Integrate factories with your classes using HasDataFactory trait
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

Have ideas for other features? [Open an issue](https://github.com/fbarrento/data-factory/issues) or submit a PR!

## Development

🧹 Keep a modern codebase with **Pint**:
```bash
composer lint
```

✅ Run refactors using **Rector**:
```bash
composer refactor
```

⚗️ Run static analysis using **PHPStan**:
```bash
composer test:types
```

✅ Run unit tests using **PEST**:
```bash
composer test:unit
```

🚀 Run the entire test suite:
```bash
composer test
```

## License

Data Factory is open-sourced software licensed under the [MIT license](LICENSE.md).
