# Contributing to Maylbox

Thank you for your interest in contributing to Maylbox! This guide will help you get started.

## Development Setup

See the [README](README.md) for full installation instructions. In short:

```bash
git clone https://github.com/echovick/maylbox.git
cd maylbox
composer setup
composer dev
```

## Workflow

1. **Fork** the repository and clone your fork
2. **Create a branch** for your change (`git checkout -b my-feature`)
3. **Make your changes**
4. **Lint your code**
   ```bash
   composer lint        # PHP (Pint)
   npm run lint         # ESLint
   npm run format       # Prettier
   ```
5. **Run the tests**
   ```bash
   composer test
   ```
6. **Commit** your changes with a clear message
7. **Push** to your fork and open a **Pull Request**

## Code Style

- **PHP** — Code style is enforced by [Laravel Pint](https://laravel.com/docs/pint) (see `pint.json`). Run `composer lint` to auto-fix.
- **JavaScript / Vue** — ESLint and Prettier are configured. Run `npm run lint` and `npm run format`.

## Pull Request Guidelines

- Keep PRs focused — one feature or fix per PR
- Reference any related issues (e.g., `Fixes #123`)
- Ensure CI passes before requesting review
- Add tests for new functionality where applicable
- Update documentation if your change affects usage

## Reporting Bugs

Open a [GitHub Issue](https://github.com/echovick/maylbox/issues) with:

- A clear title and description
- Steps to reproduce the bug
- Expected vs. actual behavior
- Your environment (PHP version, Node version, OS)

## Feature Requests

Open a [GitHub Issue](https://github.com/echovick/maylbox/issues) describing the feature, why it's useful, and any implementation ideas you have.

## Code of Conduct

This project follows the [Contributor Covenant Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code.
