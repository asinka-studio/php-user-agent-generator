# PHP User-Agent Generator

Generate realistic desktop `User-Agent` strings for modern browsers.

## Current Version

- Package version: `0.1.0`
- Namespace: `Asinka`
- Main API: `Asinka\UAGenerator::randomAgent()`

## What's New in This Version

### 1) Updated User-Agent versions and formats

The generator now uses modern UA patterns and version ranges:

- `Chrome` (Chromium-style UA)
- `Edge` (`Edg/...`)
- `Firefox` (modern `rv` + `Gecko/20100101` format)
- `Safari` (macOS Safari format)
- `Opera` (`OPR/...`, Chromium-based)

It also uses current desktop platform tokens:

- Windows 10 style: `Windows NT 10.0; Win64; x64` / `WOW64`
- macOS style: `Mac OS X 13_x`, `14_x`, `15_x`
- Linux style: `X11; Linux x86_64`

### 2) Switched naming from snake_case to camelCase

Public API naming is now camelCase:

- Old: `random_agent(...)`
- New: `randomAgent(...)`

### 3) Methods are now static

You no longer need to instantiate the class.

- Old:
```php
$generator = new \Asinka\UAGenerator();
$ua = $generator->randomAgent();
```

- New:
```php
$ua = \Asinka\UAGenerator::randomAgent();
```

### 4) Added custom exception

The library now provides and uses its own exception class:

- `Asinka\Exceptions\UAGeneratorException`

The main generator method can throw this exception for invalid or unsupported runtime cases.

## Usage

```php
<?php

use Asinka\UAGenerator;
use Asinka\Exceptions\UAGeneratorException;

require __DIR__ . '/vendor/autoload.php';

try {
    $randomUa = UAGenerator::randomAgent();
    $chromeOnWindows = UAGenerator::randomAgent(
        UAGenerator::BROWSER_CHROME,
        UAGenerator::OS_WINDOWS
    );

    echo $randomUa . PHP_EOL;
    echo $chromeOnWindows . PHP_EOL;
} catch (UAGeneratorException $e) {
    echo $e->getMessage() . PHP_EOL;
}
```

## Available Constants

### Browsers

- `UAGenerator::BROWSER_CHROME`
- `UAGenerator::BROWSER_EDGE`
- `UAGenerator::BROWSER_FIREFOX`
- `UAGenerator::BROWSER_SAFARI`
- `UAGenerator::BROWSER_OPERA`
- `UAGenerator::BROWSER_IEXPLORER` *(kept for backward compatibility and mapped to modern Edge behavior)*

### Operating Systems

- `UAGenerator::OS_WINDOWS`
- `UAGenerator::OS_MAC`
- `UAGenerator::OS_LINUX`

## Migration Notes

If you are upgrading from an older version:

1. Replace `random_agent(...)` with `randomAgent(...)`.
2. Replace object calls with static calls:
   - from `$generator->randomAgent(...)`
   - to `UAGenerator::randomAgent(...)`
3. Update exception handling to catch `UAGeneratorException` where needed.