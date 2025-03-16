# Laravel Scaffold Feature

A convenient Laravel package to scaffold structured feature directories and files interactively from Artisan command prompts.

---

## Installation

Install via Composer:

```bash
composer require stackrats/laravel-scaffold-feature
```

Laravel automatically registers the service provider.

---

## Usage

To scaffold a new feature, simply run:

```bash
php artisan scaffold:feature
```

You'll be prompted interactively for:

- Root directory (`App/Features`, `App/Shared/Features`, or `App/`)
- Optional subdirectory (e.g., `KnowledgeBase`)
- Feature name (PascalCase)
- API route method (`post`, `get`, `put`, `delete`)
- Additional options depending on the method
- Which directories/files to scaffold

After completing the prompts, your feature structure will be automatically created.

---

## Publishing Templates

Optionally, you can publish the stub templates to customize them within your Laravel application:

```bash
php artisan vendor:publish --tag=laravel-scaffold-feature
```

The templates will be copied to:

```
resources/templates/vendor/laravel-scaffold-feature
```

Feel free to modify them according to your project's requirements.

---

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
