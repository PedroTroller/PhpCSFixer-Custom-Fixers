#PHP-CS-FIXER : Custom fixers

[![Build Status](https://travis-ci.org/PedroTroller/PhpCSFixer-Custom-Fixers.svg?branch=master)](https://travis-ci.org/PedroTroller/PhpCSFixer-Custom-Fixers)



##Installation

```bash
composer require pedrotroller/php-cs-custom-fixer --dev
```

##PHPSpec Fixers

###PHPSpec scenario scope fixer

Will remove `public` scope in spec functions.

Configuration
-------------
```php
<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        //...
        'phpspec-scenario-scope',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\PhpspecScenarioScopeFixer())
    ->finder($finder)
;
```
###PHPSpec scenario name fixer

Will underscorecase all spec functions name

Configuration
-------------
```php
<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        //...
        'phpspec-name-underscorecase',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\PhpspecScenarioNameUnderscorecaseFixer())
    ->finder($finder)
;
```

###PHPSpec fixer

Will apply all phpspec fixers.

Configuration
-------------
```php
<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        //...
        'phpspec',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\PhpspecFixer())
    ->finder($finder)
;
```
