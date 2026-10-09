# Build your rule list

```php
<?php

use PedroTroller\CS\Fixer\Fixers;
use PedroTroller\CS\Fixer\RuleSetFactory;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules(RuleSetFactory::create()
        ->symfony()                 // Activate the @Symfony ruleset
        ->phpCsFixer()              // Activate the @PhpCsFixer ruleset
        ->php(8.2, risky: true)     // Activate php 8.2 risky rules
        ->pedrotroller(risky: true) // Activate my own ruleset (with risky rules)
        ->enable('ordered_imports') // Add an other rule
        ->disable('yoda_style')     // Disable a rule
        ->getRules()
    )
    ->registerCustomFixers(new Fixers())
    ->setFinder(
        PhpCsFixer\Finder::create()->in(__DIR__)
    )
;
```

## Methods

### `->auto([bool $risky = false])`

Activate the `@auto` rule set, or `@auto` and `@auto:risky` depending on the `$risky` argument. Both are resolved from the `composer.json` of the analysed project:

- `@auto` applies the newest `@PER-CS` and `@autoPHPMigration`;
- `@auto:risky` only carries the risky counterparts: `@PER-CS:risky`, `@autoPHPMigration:risky` and `@autoPHPUnitMigration:risky`.

### `->per([int|float $version = null, [bool $risky = false]])`

Activate the `@PER-CS` rule set (the newest PER-CS version) when no version is given, or a specific one (`@PER-CS1x0`, `@PER-CS2x0`, `@PER-CS3x0`, ...). With `risky: true`, the risky variant (`@PER-CS:risky`, `@PER-CS2x0:risky`, ...) is activated instead.

Example:

```php
    RuleSetFactory::create()
        ->per()
        ->per(risky: true)
        ->per(2.0)
        ->per(2.0, risky: true)
        ->getRules()
    ;
```

### `->psr1()`

Activate the `@PSR1` rule set.

### `->psr2()`

Activate the `@PSR2` rule set.

### `->psr12([bool $risky = false])`

Activate the `@PSR12` rule set, or `@PSR12` and `@PSR12:risky` depending on the `$risky` argument.

### `->symfony([bool $risky = false])`

Activate the `@Symfony` rule set, or `@Symfony` and `@Symfony:risky` depending on the `$risky` argument.

### `->phpCsFixer([bool $risky = false])`

Activate the `@PhpCsFixer` rule set, or `@PhpCsFixer` and `@PhpCsFixer:risky` depending on the `$risky` argument.

### `->doctrineAnnotation()`

Activate the `@DoctrineAnnotation` rule set.

### `->php([float $version = null, [bool $risky = false]])`

Activate fixers and rules related to a PHP version, including risky ones or not depending on the `$risky` argument.

Without a version, the `@autoPHPMigration` rule set (and `@autoPHPMigration:risky` with `risky: true`) is activated: the target PHP version is read from the `composer.json` of the analysed project.

Example:

```php
    RuleSetFactory::create()
        ->php()
        ->php(risky: true)
        ->php(7.4)
        ->php(8.2, risky: true)
        ->getRules()
    ;
```

### `->phpUnit([float $version = null, [bool $risky = false]])`

Activate fixers and rules related to a PHPUnit version, including risky ones or not depending on the `$risky` argument.

Without a version, the `@autoPHPUnitMigration:risky` rule set is activated: the target PHPUnit version is read from the `composer.json` of the analysed project. php-cs-fixer only ships a risky variant of it, so `->phpUnit()` without `risky: true` adds nothing.

Example:

```php
    RuleSetFactory::create()
        ->phpUnit(risky: true)
        ->phpUnit(5.2)              // There is no non-risky rule for the moment
        ->phpUnit(10.0, risky: true)
        ->getRules()
    ;
```

### `->pedrotroller([bool $risky = false])`

Activate all rules of this library, including risky ones or not depending on the `$risky` argument.

### `->enable(string $name, array $config = null)`

Enable a rule.

Example:

```php
    RuleSetFactory::create()
        ->enable('ordered_class_elements')
        ->enable('ordered_imports')
        ->enable('phpdoc_add_missing_param_annotation', ['only_untyped' => true])
        ->getRules()
    ;
```

### `->disable(string $name)`

Disable a rule.

Example:

```php
    RuleSetFactory::create()
        ->disable('ordered_class_elements')
        ->getRules()
    ;
```
