# Contributing to Guard Laravel

Thank you for considering contributing to Guard Laravel! We welcome contributions from the community.

## Development Setup

1. Fork the repository
2. Clone your fork locally
3. Install dependencies:

```bash
composer install
```

## Running Tests

Make sure tests pass before submitting a pull request:

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage
```

## Code Style

Ensure your code follows Laravel Pint standards:

```bash
# Check code style
composer format:check

# Fix code style
composer format
```

## Static Analysis

Check for potential issues using Larastan:

```bash
composer analyse
```

## Pull Request Process

1. Create a new branch from `main`
2. Write tests for your changes
3. Ensure all tests pass (`composer test`)
4. Ensure code style passes (`composer format:check`)
5. Ensure static analysis passes (`composer analyse`)
6. Submit a pull request with a clear description of changes

### PR Guidelines

- Write clear, descriptive commit messages
- Add tests for new features or bug fixes
- Update documentation as needed
- Follow PSR-12 coding standards
- Keep changes focused and atomic

## Reporting Bugs

Before creating bug reports:

1. Check existing issues to avoid duplicates
2. Provide a minimal reproduction case
3. Include your PHP and Laravel versions
4. Share relevant code snippets
5. Describe expected vs actual behavior

## Suggesting Features

We welcome feature suggestions! Please:

1. Check existing issues for similar requests
2. Explain the use case and problem it solves
3. Consider if it fits the package's scope
4. Provide examples of how the feature would work

## Documentation

Help improve documentation by:

- Fixing typos or grammar
- Clarifying confusing sections
- Adding more examples
- Updating outdated information

## Code of Conduct

- Be respectful and inclusive
- Focus on constructive feedback
- Help others learn and grow
- No harassment or discrimination

## Questions?

- Open an issue for questions
- Join discussions for community support
- Check existing documentation first

Thank you for contributing to Guard Laravel! 🙏
