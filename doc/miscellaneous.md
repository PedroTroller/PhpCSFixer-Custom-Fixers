###Force Multi-Bytes extention function usage

Force the usage of function implemented into the `mbstring` php extension.

For example : 

```php
var $foo = strtolower('FOO');
```

Becomes

```php
var $foo = mb_strtolower('FOO');
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
        'mbstring',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\MbstringFixer())
    ->finder($finder)
;
```
