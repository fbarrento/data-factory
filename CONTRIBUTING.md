# Contributing to Data Factory

Thank you for considering contributing to Data Factory! This document will guide you through the process.

## Code of Conduct

Be respectful, constructive, and professional. We're all here to make this project better.

## Getting Started

### Prerequisites

- PHP 8.2, 8.3, or 8.4
- Composer
- Git

### Setup

1. Fork the repository on GitHub
2. Clone your fork locally:
   ```bash
   git clone https://github.com/YOUR_USERNAME/data-factory.git
   cd data-factory
   ```
3. Install dependencies:
   ```bash
   composer install
   ```

## Development Workflow

### Before You Start

1. Create a new branch for your feature or fix:
   ```bash
   git checkout -b feature/your-feature-name
   ```
2. Make sure all tests pass:
   ```bash
   composer test
   ```

### Making Changes

1. Write your code following the existing code style
2. Add tests for any new functionality
3. Update documentation if needed
4. Run the quality tools to ensure your code meets our standards

### Quality Standards

Data Factory maintains high quality standards:

- **100% code coverage** - All code must be tested
- **100% type coverage** - All functions must have type hints
- **PHPStan level 9** - Strictest static analysis
- **Laravel Pint** - Consistent code formatting
- **Rector** - Modern PHP patterns

### Running Quality Tools

#### Format code with Pint
```bash
composer lint
```

#### Run Rector refactoring
```bash
composer refactor
```

#### Run PHPStan static analysis
```bash
composer test:types
```

#### Run PEST unit tests with coverage
```bash
composer test:unit
```

#### Check type coverage
```bash
composer test:type-coverage
```

#### Check for typos
```bash
composer test:typos
```

#### Verify Pint formatting (without fixing)
```bash
composer test:lint
```

#### Dry-run Rector (without fixing)
```bash
composer test:refactor
```

#### Run the full test suite
```bash
composer test
```

This runs all checks: unit tests, type coverage, static analysis, formatting, refactoring, and typo detection.

### Before Submitting

Make sure your changes pass all quality checks:

```bash
composer test
```

If this passes, you're good to go!

## Submitting Changes

### Pull Request Process

1. Push your changes to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```

2. Create a Pull Request on GitHub with:
   - Clear title describing the change
   - Description of what changed and why
   - Any related issue numbers
   - Make sure to follow the [template](.github/PULL_REQUEST_TEMPLATE.md)

3. Wait for review and address any feedback

### Pull Request Guidelines

- **One feature per PR** - Keep changes focused
- **Write tests** - All new code must have tests
- **Update documentation** - Keep docs in sync with code
- **Follow existing patterns** - Look at existing code for guidance
- **Add type hints** - All parameters and return types must be typed
- **Write clear commit messages** - Describe what and why, not how
- **Follow SemVer** - We follow [Semantic Versioning](http://semver.org/)

## Testing Guidelines

### Writing Tests

- Use PEST syntax
- Follow the Arrange-Act-Assert pattern
- Test edge cases and error conditions
- Keep tests focused and readable
- Use descriptive test names

Example:
```php
it('creates multiple instances with count', function () {
    $factories = VehicleFactory::new()->count(5)->make();

    expect($factories)->toHaveCount(5)
        ->each->toBeInstanceOf(Vehicle::class);
});
```

### Test Coverage

All new code must be covered by tests. Run coverage report:

```bash
composer test:unit
```

Coverage report will be generated in `coverage/` directory.

## Documentation Guidelines

### When to Update Documentation

- Adding new features
- Changing existing behavior
- Adding new public methods
- Changing method signatures

### Documentation Location

- **README.md** - Overview and quick start
- **docs/** - Detailed feature documentation
- **Inline comments** - Complex logic explanations
- **PHPDoc blocks** - All public methods

### Documentation Style

- Use clear, concise language
- Include code examples
- Show both what to do and what not to do
- Keep examples realistic and practical

## Code Style Guidelines

### PHP Code Style

We use Laravel Pint with default configuration:

- PSR-12 compliant
- 4 spaces for indentation
- No trailing whitespace
- Unix line endings (LF)

Run Pint to automatically fix formatting:
```bash
composer lint
```

### Type Hints

All parameters and return types must have type hints:

```php
// ✅ Good
public function make(array $attributes = []): object|array
{
    // ...
}

// ❌ Bad
public function make($attributes = [])
{
    // ...
}
```

### Modern PHP Features

Use modern PHP 8.2+ features:

- Named arguments
- Constructor property promotion
- Readonly properties
- Match expressions
- Null-safe operator

## Need Help?

- **Questions?** Open a [GitHub Discussion](https://github.com/fbarrento/data-factory/discussions)
- **Bug reports?** Open a [GitHub Issue](https://github.com/fbarrento/data-factory/issues)
- **Feature ideas?** Open a [GitHub Issue](https://github.com/fbarrento/data-factory/issues)

## License

By contributing, you agree that your contributions will be licensed under the MIT License.
