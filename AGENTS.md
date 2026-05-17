# Repository Guidelines

## Project Structure & Module Organization

This repository is a Symfony bundle for dynamic key/value lists. Bundle code lives in `src/` under the `Survos\KeyValueBundle\` namespace. Main areas are `src/Command/` for `survos:kv:*` console commands, `src/Entity/` and `src/Repository/` for Doctrine storage, `src/Type/` for value type handling, `src/Validator/Constraints/` for custom validators, and `src/Utils/` for shared helpers.

Tests live in `tests/` under `Survos\KeyValueBundle\Tests\`. Keep new tests close to the code path they cover, for example validator tests in `tests/Validator/Constraints/`.

## Build, Test, and Development Commands

Install dependencies with:

```bash
composer install
```

Run the test suite:

```bash
composer phpunit
```

Run static analysis and style checks:

```bash
composer phpstan
composer cs-check
```

Apply coding style fixes:

```bash
composer cs-fix
```

Generate coverage reports with `composer coverage` or text coverage with `composer phpunit-coverage-text`.

## Coding Style & Naming Conventions

Use PHP 8.4+ with `declare(strict_types=1);` in every PHP file. Follow PSR-1/PSR-2 plus the Slevomat rules configured in `phpcs.xml`. Use four-space indentation, typed properties, typed parameters, and explicit return types.

Class names use PascalCase and should match their file names, such as `KeyValueManager.php`. Methods and properties use camelCase. Symfony commands should use attributes and clear command names, for example `survos:kv:add`.

Prefer modern Symfony bundle structure centered in `src/SurvosKeyValueBundle.php`. Do not add legacy `DependencyInjection/*Extension.php` or sidecar service config unless there is a specific technical need.

## Testing Guidelines

The project uses PHPUnit through Symfony PHPUnit Bridge. Name test classes after the unit under test with a `Test` suffix, for example `IsNotBlacklistedValidatorTest`. Add tests for new command behavior, validators, repository queries, and edge cases around duplicate or missing key/value entries.

Before opening a pull request, run at least:

```bash
composer phpunit
composer phpstan
composer cs-check
```

## Commit & Pull Request Guidelines

Recent commits use short, imperative summaries such as `refactor data-contracts more for AI` or `drop extends Command`. Keep commit subjects concise and focused on the change.

Pull requests should include a brief description, the reason for the change, and the commands run for verification. Link related issues when applicable. For behavior changes, include an example command or code snippet showing the new expected usage.

## Security & Configuration Tips

Do not commit local secrets, database URLs, or generated coverage output. Keep bundle changes backward compatible where possible because this package is intended for reuse across Symfony applications.
