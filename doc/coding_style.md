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

### Variable Assign And Return

Remove useless assigned and returned variables

For example : 

```php
function theFunction()
{
    $var = 'foo';

    return $var;
}
```

Becomes

```php
function theFunction()
{
    return 'foo';
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
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        //...
        'variable_assign_and_return',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\VariableAssignAndReturnFixer())
    ->finder($finder)
;
```

### Property Assign And Return

Remove useless assigned and returned properties

For example : 

```php
function theFunction()
{
    $this->var = 'foo';

    return $this->var;
}
```

Becomes

```php
function theFunction()
{
    return $this->var = 'foo';
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
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        //...
        'property_assign_and_return',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\PropertyAssignAndReturnFixer())
    ->finder($finder)
;
```

### General Assign And Return

**Variable Assign And Return** AND **Property Assign And Return**.

For example : 

```php
function theFunction()
{
    $this->var = 'foo';

    return $this->var;
}

function theOtherFunction()
{
    $var = 'foo';

    return $var;
}
```

Becomes

```php
function theFunction()
{
    return $this->var = 'foo';
}

function theOtherFunction()
{
    return 'foo';
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
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        //...
        'assign_and_return',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\AssignAndReturnFixer())
    ->finder($finder)
;
```
