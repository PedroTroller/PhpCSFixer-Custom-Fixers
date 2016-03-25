### Line Break Between Statements

Transform multiline docblocks with only one comment into a singleline docblock.

For example : 

```php
for (['foo', 'bar'] as $str) {
    // ...
}
if (true === false) {
    // ...
}


while (true) {
    // ...
}
```

Becomes

```php
for (['foo', 'bar'] as $str) {
    // ...
}

if (true === false) {
    // ...
}

while (true) {
    // ...
}
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
        'line_break_between_statements',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\LineBreakBetweenStatementsFixer())
    ->finder($finder)
;
```
