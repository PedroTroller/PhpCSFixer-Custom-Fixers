# PHP-CS-FIXER : Custom fixers

[![Build Status](https://travis-ci.org/PedroTroller/PhpCSFixer-Custom-Fixers.svg?branch=master)](https://travis-ci.org/PedroTroller/PhpCSFixer-Custom-Fixers)
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

### Fixes

```diff
{{{diff}}}
```
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
