# EDD ConvertKit Stubs

[![Test](https://github.com/artkrsk/edd-convertkit-stubs/actions/workflows/integrate.yml/badge.svg)](https://github.com/artkrsk/edd-convertkit-stubs/actions/workflows/integrate.yml)
[![Latest Release](https://img.shields.io/github/v/release/artkrsk/edd-convertkit-stubs)](https://github.com/artkrsk/edd-convertkit-stubs/releases/latest)
[![PHP Version](https://img.shields.io/packagist/dependency-v/arts/edd-convertkit-stubs/php)](https://packagist.org/packages/arts/edd-convertkit-stubs)
[![Buy Me A Coffee](https://img.shields.io/badge/Buy%20Me%20A%20Coffee-support-yellow?logo=buy-me-a-coffee)](https://buymeacoffee.com/artemsemkin)

Comprehensive PHPStan stubs for EDD ConvertKit — the commercial add-on for Easy Digital Downloads that subscribes customers to ConvertKit forms on purchase.

Get full IDE autocomplete, IntelliSense, and type safety when wiring ConvertKit syncs into EDD-powered WordPress plugins and themes.

## Features

- Full IDE autocomplete for the EDD ConvertKit singleton and its public methods
- Type safety and static analysis with PHPStan
- Catch errors before runtime when calling `EDD_ConvertKit::instance()->subscribe_email()` and friends
- Pulls EDD core stubs as a transitive dependency (`arts/easy-digital-downloads-stubs`) so EDD types referenced by ConvertKit resolve automatically

## Requirements

- PHP 8.0 or higher
- PHPStan for static analysis
- Automatically pulls in EDD core, WordPress, and WP-CLI stubs as dependencies

## Installation

```bash
composer require --dev arts/edd-convertkit-stubs
```

## Usage with PHPStan

Add to your `phpstan.neon`:

```yaml
parameters:
    bootstrapFiles:
        - vendor/php-stubs/wordpress-stubs/wordpress-stubs.php
        - vendor/php-stubs/wp-cli-stubs/wp-cli-stubs.php
        - vendor/arts/easy-digital-downloads-stubs/easy-digital-downloads-stubs.php
        - vendor/arts/edd-convertkit-stubs/edd-convertkit-stubs.php
```

## Regenerating Stubs

EDD ConvertKit is a commercial add-on distributed by Sandhills Development; it is not available on WordPress.org. To generate stubs you need a local copy of the plugin source.

1. Copy `.env.example` to `.env`
2. Set `EDD_CONVERTKIT_PATH` to your local EDD ConvertKit installation
3. Run: `composer generate`

```bash
cp .env.example .env
# Edit .env with your path
composer generate
```

## 💖 Support

If you find this package useful, consider buying me a coffee:

<a href="https://buymeacoffee.com/artemsemkin" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a>

---

Made with ❤️ by [Artem Semkin](https://artemsemkin.com)
