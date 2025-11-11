# Installation

## Requirements

- PHP 8.4 or higher
- Composer

## Install via Composer

Install Data Factory as a development dependency:

```bash
composer require fbarrento/data-factory --dev
```

## Verify Installation

Create a simple factory to verify everything is working:

```php
<?php

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

// Create a user
$user = UserFactory::new()->make();

var_dump($user);
```

## What's Included

Data Factory automatically includes:

- **FakerPHP** - For generating realistic fake data
- **Type-safe factories** - Full PHP 8.4+ generic support
- **No framework dependencies** - Use with any PHP project

## Next Steps

- [Basic Usage](basic-usage.md) - Create your first factory
- [Faker Integration](faker.md) - Learn about generating fake data
