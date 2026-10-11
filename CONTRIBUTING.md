# Contributing to Liberu CMS

Thank you for your interest in contributing to the Liberu CMS platform! This document provides guidelines and instructions for contributing.

## Before You Start

- **Branch from `main`**: Always create a new branch from the latest `main` branch for your changes.
- **Focused changes**: Keep pull requests focused on a single feature or fix. Separate concerns into separate PRs.
- **Check existing issues**: Before starting work, check if there's an existing issue or PR related to your change.

## Development Setup

1. Fork and clone the repository.
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install JavaScript dependencies:
   ```bash
   npm install
   ```
4. Copy the environment file:
   ```bash
   cp .env.example .env
   ```
5. Generate the application key:
   ```bash
   php artisan key:generate
   ```
6. Run database migrations:
   ```bash
   php artisan migrate
   ```
7. Build frontend assets:
   ```bash
   npm run dev
   ```

## Running Tests

We use Pest for testing and Laravel Pint for code style:

```bash
# Run the test suite
vendor/bin/pest

# Check code style
vendor/bin/pint --test

# Fix code style
vendor/bin/pint
```

## Code Style

- **Laravel Pint**: We use Laravel Pint for PHP code formatting. Run `vendor/bin/pint` before committing.
- **PHPStan**: Static analysis is performed with PHPStan. Ensure your code passes analysis.
- **Conventions**: Follow Laravel conventions and the existing codebase patterns.

## Pull Request Process

1. Create a feature branch from `main`.
2. Make your changes with clear, descriptive commit messages.
3. Ensure all tests pass and code style is clean.
4. Update documentation if your changes affect usage.
5. Open a pull request with a clear description of the changes and their purpose.
6. Address any review feedback promptly.

## Documentation Requirements

- Update relevant documentation in the `docs/` directory when adding or changing features.
- Include inline code comments for complex logic.
- Update the README if you're adding new setup steps or configuration options.

## Security Reporting

Please do not report security vulnerabilities through public issues. See [SECURITY.md](SECURITY.md) for how to report them privately.
