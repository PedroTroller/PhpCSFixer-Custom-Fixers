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
        'phpspec_scenario_scope',
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
        'phpspec_name_underscorecase',
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

###Single comment collapsed fixer

Transform multiline docblocks with only one comment into a singleline docblock.

The **Single comment expanded fixer** the the exact opposite of this fixer.

For example : 

```php
/**
 * @var string
 */
```

Becomes

```php
/** @var string */
```

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
        'single_comment_collapsed',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\SingleCommentCollapsedFixer())
    ->finder($finder)
;
```

###Single comment expanded fixer

Transform singleline docblocks with only one comment into a multiline docblock.

The **Single comment collasped fixer** the the exact opposite of this fixer.

For example : 

```php
/** @var string */
```

Becomes

```php
/**
 * @var string
 */
```

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
        'single_comment_expanded',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\SingleCommentExpandedFixer())
    ->finder($finder)
;
```
