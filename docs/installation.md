# Installation

## Requirements

- PHP 8.4 or higher
- Composer
- A PHP testing framework (PEST, PHPUnit, Codeception, etc.)

## Install via Composer

Install Data Factory as a development dependency for your test suite:

```bash
composer require fbarrento/data-factory --dev
```

> **Note**: The `--dev` flag installs it as a development dependency since factories are primarily used for testing.

## Verify Installation

Create a simple factory in your tests directory and use it in a PEST test:

```php
<?php
// tests/Factories/UserFactory.php

use FBarrento\DataFactory\Factory;

class UserFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'name' => $this->fake->name(),
            'email' => $this->fake->email(),
        ];
    }
}
```

```php
<?php
// tests/Feature/UserTest.php

it('creates a user', function () {
    $user = UserFactory::new()->make();

    expect($user->name)->toBeString()
        ->and($user->email)->toContain('@');
});
```

## What's Included

Data Factory automatically includes:

- **FakerPHP** - For generating realistic fake data
- **Type-safe factories** - Full PHP 8.4+ generic support
- **No framework dependencies** - Use with any PHP project

## Next Steps

- [Why Use Factories?](why-factories.md) - Learn how factories improve your tests
- [Basic Usage](basic-usage.md) - Create your first factory
- [Testing Guide](testing.md) - Complete guide to using factories in PEST tests
