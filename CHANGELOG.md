# Changelog

All notable changes to `data-factory` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-01-11

### Changed
- **BREAKING**: Moved `HasDataFactory` trait to `FBarrento\DataFactory\Concerns\HasDataFactory`
  - Update imports: `use FBarrento\DataFactory\Concerns\HasDataFactory;`
  - This follows Laravel's convention of placing traits in a `Concerns` subdirectory

### Added
- Complete Laravel Cloud API examples with deployment status enum
- `DeploymentStatus` enum example in advanced documentation
- Enhanced documentation showing enum usage in factories

## [1.0.0] - 2025-01-11

### Added
- Initial stable release
- Core factory pattern for PHP objects and arrays
- Faker integration for realistic fake data generation
- States feature for reusable variations
- Sequences feature for alternating values across instances
- Nested factories support for complex object graphs
- Full PHP 8.4 enum support with `randomElement()`
- `HasDataFactory` trait for easy factory integration
- `ArrayFactory` for type-safe array generation
- Comprehensive documentation with examples
- 100% test coverage with 57 tests
- PHPStan level max with 100% type coverage
- PHP 8.2, 8.3, and 8.4 compatibility

### Quality Assurance
- Laravel Pint for code formatting
- Rector for automated refactoring
- PHPStan for static analysis
- PEST for unit testing
- Peck for typo detection

### Documentation
- Complete README with quick start guide
- Detailed documentation for all features
- Real-world examples
- Roadmap for future features

[1.1.0]: https://github.com/fbarrento/data-factory/releases/tag/v1.1.0
[1.0.0]: https://github.com/fbarrento/data-factory/releases/tag/v1.0.0
