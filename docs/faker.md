# Faker Integration

Data Factory includes [FakerPHP](https://fakerphp.github.io/) to generate realistic fake data. The Faker instance is available via `$this->fake` in your factory's `definition()` method and state methods.

## Using Faker

Access Faker through the `$this->fake` property:

```php
<?php

use FBarrento\DataFactory\Factory;

class ApplicationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
            'slug' => $this->fake->slug(),
            'region' => $this->fake->randomElement(['us-east-1', 'us-east-2', 'eu-west-1']),
            'created_at' => $this->fake->dateTime(),
        ];
    }
}
```

## Common Faker Methods

### Identifiers

```php
$this->fake->uuid()           // "f3e9a1c4-7b2d-4a8c-9e1f-5d6c8a9b2e4f"
$this->fake->sha1()           // "4a3b2c1d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t"
$this->fake->md5()            // "5d41402abc4b2a76b9719d911017c592"
```

### Names & Text

```php
$this->fake->name()           // "Jane Doe"
$this->fake->company()        // "Acme Corporation"
$this->fake->word()           // "voluptatem"
$this->fake->words(3, true)   // "dolor sit amet"
$this->fake->sentence()       // "Sed ut perspiciatis unde omnis."
$this->fake->paragraph()      // "Lorem ipsum dolor sit amet..."
$this->fake->text(200)        // 200 character text block
```

### Internet & Email

```php
$this->fake->email()          // "john.doe@example.com"
$this->fake->safeEmail()      // "jane@example.org"
$this->fake->domainName()     // "example.com"
$this->fake->url()            // "https://example.com"
$this->fake->ipv4()           // "192.168.1.1"
$this->fake->slug()           // "my-awesome-application"
```

### Dates & Times

```php
$this->fake->dateTime()                    // DateTime object
$this->fake->dateTimeBetween('-1 year')    // DateTime in last year
$this->fake->iso8601()                     // "2024-11-07T05:31:56+0000"
$this->fake->unixTime()                    // 1699336316
```

### Numbers

```php
$this->fake->randomDigit()              // 0-9
$this->fake->randomNumber(5)            // 12345
$this->fake->numberBetween(1, 100)      // 42
$this->fake->randomFloat(2, 0, 100)     // 42.42
$this->fake->boolean()                  // true or false
$this->fake->boolean(70)                // true 70% of the time
```

### Addresses

```php
$this->fake->streetAddress()   // "123 Main Street"
$this->fake->city()            // "New York"
$this->fake->postcode()        // "12345"
$this->fake->country()         // "United States"
```

### Random Elements

```php
$this->fake->randomElement(['pending', 'running', 'succeeded', 'failed'])
$this->fake->randomElements(['a', 'b', 'c', 'd'], 2)  // ['b', 'd']
$this->fake->shuffleArray(['one', 'two', 'three'])
```

## Real-World Example: Environment Factory

```php
<?php

use FBarrento\DataFactory\Factory;

class EnvironmentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->word(),
            'slug' => $this->fake->slug(),
            'status' => $this->fake->randomElement([
                'deploying',
                'running',
                'hibernating',
                'stopped'
            ]),
            'vanity_domain' => $this->fake->domainName(),
            'php_major_version' => $this->fake->randomElement(['8.2', '8.3', '8.4']),
            'node_version' => $this->fake->randomElement(['20', '22']),
            'uses_web_server' => $this->fake->boolean(80),
            'uses_octane' => $this->fake->boolean(30),
            'uses_hibernation' => $this->fake->boolean(50),
            'created_at' => $this->fake->dateTimeBetween('-6 months'),
        ];
    }
}
```

## Using Faker in States

You can also use Faker in your state methods:

```php
public function production(): static
{
    return $this->state([
        'name' => 'production',
        'slug' => 'production',
        'php_major_version' => '8.4',  // Latest version for production
        'uses_octane' => true,
        'created_at' => $this->fake->dateTimeBetween('-2 years', '-1 year'),
    ]);
}
```

## Localization

Faker supports multiple locales. You can customize this when creating your factory if needed:

```php
protected function definition(): array
{
    $faker = \Faker\Factory::create('fr_FR');  // French locale

    return [
        'name' => $faker->company(),  // French company name
        'city' => $faker->city(),     // French city
    ];
}
```

However, for most use cases, the default `$this->fake` with the English locale is sufficient.

## Learn More

- [FakerPHP Documentation](https://fakerphp.github.io/) - Complete list of available formatters
- [States](states.md) - Define reusable state variations with Faker
- [Advanced Examples](advanced-examples.md) - Complex real-world examples
