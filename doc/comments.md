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
