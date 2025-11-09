# Data Factory - Project Context

## Overview
This is a test data factory library for PHP by Francisco Barrento. It provides a modern PHP package development setup with testing, linting, and static analysis tools.

## Tech Stack
- **PHP**: 8.4+
- **Testing**: PEST (unit tests with 100% coverage requirement)
- **Linting**: Laravel Pint
- **Static Analysis**: PHPStan
- **Refactoring**: Rector
- **Type Coverage**: PEST Type Coverage Plugin (100% required)
- **Typo Detection**: Peck

## Project Structure
- `src/` - Main source code (PSR-4: `FBarrento\DataFactory`)
- `tests/` - Test files (PSR-4: `Tests`)
- `composer.json` - Dependencies and scripts
- `rector.php` - Rector configuration
- `peck.json` - Peck typo detection configuration

## Available Commands

### Quality Tools
```bash
composer lint              # Format code with Pint
composer refactor          # Run Rector refactoring
composer test:types        # PHPStan static analysis
composer test:unit         # Run PEST unit tests with coverage
composer test:type-coverage # Check 100% type coverage
composer test:typos        # Check for typos with Peck
composer test:lint         # Verify Pint formatting
composer test:refactor     # Dry-run Rector
composer test              # Run full test suite
```

## Quality Standards
- 100% code coverage required
- 100% type coverage required
- All code must pass Pint formatting
- All code must pass PHPStan analysis
- All code must pass Rector checks
- No typos allowed (checked by Peck)

## Development Guidelines
- Follow PSR-4 autoloading standards
- Write tests for all new code
- Ensure type hints on all functions
- Run the full test suite before committing
- Use Pint for consistent code style
- Use Rector for automated refactoring