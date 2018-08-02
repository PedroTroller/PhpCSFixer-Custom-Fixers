# Build your rule list

```php
<?php

use PedroTroller\CS\Fixer\Fixers;
use PedroTroller\CS\Fixer\RuleSetFactory;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules(RuleSetFactory::create()
        ->symfony()                 // Activate the @Symfony ruleset
        ->php(5.6, true)            // Activate php 5.6 risky rules
        ->pedrotroller(true)        // Activate my own ruleset (with risky rules)
        ->enable('ordered_imports') // Add an other rule
        ->getRules()
    )
    ->registerCustomFixers(new Fixers())
    ->setFinder(
        PhpCsFixer\Finder::create()->in(__DIR__)
    )
;
```

## Methods

### `->psr0()`

Activate the `@psr0` ruleset.

### `->psr1()`

Activate the `@psr0` ruleset.

### `->psr2()`

Activate the `@psr0` ruleset.

### `->psr4()`

Activate the `@psr0` ruleset.

### `->symfony([bool $risky = false])`

Activate the `@Symfony` or `@Symfony:risky` depending of the `$risky` argument.

### `->php(float $version, [bool $risky = false])`

Activate fixers related to a PHP version including risky of not depending of the `$risky` argument.

Example: 

```php
    RuleSetFactory::create()
        ->php(5.6)
        ->php(5.6, true)
        ->php(7.0)
        ->php(7.0, true)
        ->php(7.1)
        ->php(7.1, true)
        ->php(7.2)
        ->php(7.2, true)
        ->getRules()
    ;
```

### `->pedrotroller([bool $risky = false])`

Activate all rules of this library including risky of not depending of the `$risky` argument.

### `->enable(string $name, [$config = true])`

Enable a rule.

Example: 

```php
    RuleSetFactory::create()
        ->enable('ordered_class_elements')
        ->enable('ordered_imports', true) // Same as "->enable('ordered_imports')"
        ->enable('phpdoc_add_missing_param_annotation', ['only_untyped' => true])
        ->getRules()
    ;
```

### `->disable(string $name)`

Disable a rule.

Example: 

```php
    RuleSetFactory::create()
        ->disable('ordered_class_elements') // Same as "->enable('ordered_class_elements', false)"
        ->getRules()
    ;
```
