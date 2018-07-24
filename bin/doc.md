# PHP-CS-FIXER : Custom fixers

[![Latest Stable Version](https://poser.pugx.org/pedrotroller/php-cs-custom-fixer/v/stable)](https://packagist.org/packages/pedrotroller/php-cs-custom-fixer)
[![CircleCI](https://circleci.com/gh/PedroTroller/PhpCSFixer-Custom-Fixers.svg?style=svg)](https://circleci.com/gh/PedroTroller/PhpCSFixer-Custom-Fixers)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/PedroTroller/PhpCSFixer-Custom-Fixers/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PedroTroller/PhpCSFixer-Custom-Fixers/?branch=master)

# Installation

```bash
composer require pedrotroller/php-cs-custom-fixer --dev
```

### Configuration

```php
// .php_cs
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
    // ...
;

return $config;
```

# Fixers

{{#fixers}}

## {{name}}

{{doc.summary}}
{{#deprecated}}

**DEPRECATED**
{{#replacement}}
replaced by `{{replacement}}`.
{{/replacement}}
{{/deprecated}}

{{#samples}}
### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        {{#configuration}}
        '{{ name }}' => {{{configuration}}},
        {{/configuration}}
        {{^configuration}}
        '{{ name }}' => true,
        {{/configuration}}
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

{{^deprecated}}
### Fixes

```diff
{{{diff}}}
```
{{/deprecated}}
{{/samples}}

{{/fixers}}

# Contributions

Before to create a pull request to submit your contributon, you must:
 - run tests and be sure nothing is broken
 - rebuilt the documentation

## How to run tests

```bash
composer tests
```

## How to rebuild the documentation

```bash
bin/doc > README.md
```
