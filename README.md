# PHP-CS-FIXER : Custom fixers

[![CircleCI](https://circleci.com/gh/PedroTroller/PhpCSFixer-Custom-Fixers.svg?style=shield)](https://circleci.com/gh/PedroTroller/PhpCSFixer-Custom-Fixers)
[![Latest Stable Version](https://poser.pugx.org/pedrotroller/php-cs-custom-fixer/v/stable)](https://packagist.org/packages/pedrotroller/php-cs-custom-fixer)
[![License](https://poser.pugx.org/pedrotroller/php-cs-custom-fixer/license)](https://packagist.org/packages/pedrotroller/php-cs-custom-fixer)
[![Dependabot Status](https://api.dependabot.com/badges/status?host=github&repo=PedroTroller/PhpCSFixer-Custom-Fixers)](https://dependabot.com)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/PedroTroller/PhpCSFixer-Custom-Fixers/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PedroTroller/PhpCSFixer-Custom-Fixers/?branch=master)

# Installation

```bash
composer require pedrotroller/php-cs-custom-fixer --dev
```

### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
    // ...
;

return $config;
```

# Fixers

## PedroTroller/order_behat_steps

Step definition methods in Behat contexts MUST BE ordered by annotation and method name.


### Available options

 - `instanceof` (*optional*): Parent class or interface of your behat context classes.
    - default: `Behat\Behat\Context\Context`

### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/order_behat_steps' => true,
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/order_behat_steps')
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
     }                                                                           //
                                                                                 //
     /**                                                                         //
-     * @Then the response should be received                                    //
+     * @BeforeScenario                                                          //
      */                                                                         //
-    public function theResponseShouldBeReceived()                               //
+    public function reset()                                                     //
     {                                                                           //
         // ...                                                                  //
     }                                                                           //
                                                                                 //
     /**                                                                         //
-     * @When a demo scenario sends a request to :path                           //
+     * @Given I am on the homepage                                              //
      */                                                                         //
-    public function aDemoScenarioSendsARequestTo($path)                         //
+    public function iAmOnTheHomepage()                                          //
     {                                                                           //
         // ...                                                                  //
     }                                                                           //
                                                                                 //
     /**                                                                         //
-     * @Given I am on the homepage                                              //
+     * @When a demo scenario sends a request to :path                           //
      */                                                                         //
-    public function iAmOnTheHomepage()                                          //
+    public function aDemoScenarioSendsARequestTo($path)                         //
     {                                                                           //
         // ...                                                                  //
     }                                                                           //
                                                                                 //
     /**                                                                         //
-     * @BeforeScenario                                                          //
+     * @Then the response should be received                                    //
      */                                                                         //
-    public function reset()                                                     //
+    public function theResponseShouldBeReceived()                               //
     {                                                                           //
         // ...                                                                  //
     }                                                                           //
 }                                                                               //
                                                                                 //
```
### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/order_behat_steps' => [ 'instanceof' => [ 'Behat\Behat\Context\Context' ] ],
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/order_behat_steps', [ 'instanceof' => [ 'Behat\Behat\Context\Context' ] ])
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
     }                                                                           //
                                                                                 //
     /**                                                                         //
-     * @Then the response should be received                                    //
+     * @BeforeScenario                                                          //
      */                                                                         //
-    public function theResponseShouldBeReceived()                               //
+    public function reset()                                                     //
     {                                                                           //
         // ...                                                                  //
     }                                                                           //
                                                                                 //
     /**                                                                         //
-     * @When a demo scenario sends a request to :path                           //
+     * @Given I am on the homepage                                              //
      */                                                                         //
-    public function aDemoScenarioSendsARequestTo($path)                         //
+    public function iAmOnTheHomepage()                                          //
     {                                                                           //
         // ...                                                                  //
     }                                                                           //
                                                                                 //
     /**                                                                         //
-     * @Given I am on the homepage                                              //
+     * @When a demo scenario sends a request to :path                           //
      */                                                                         //
-    public function iAmOnTheHomepage()                                          //
+    public function aDemoScenarioSendsARequestTo($path)                         //
     {                                                                           //
         // ...                                                                  //
     }                                                                           //
                                                                                 //
     /**                                                                         //
-     * @BeforeScenario                                                          //
+     * @Then the response should be received                                    //
      */                                                                         //
-    public function reset()                                                     //
+    public function theResponseShouldBeReceived()                               //
     {                                                                           //
         // ...                                                                  //
     }                                                                           //
 }                                                                               //
                                                                                 //
```

## PedroTroller/ordered_with_getter_and_setter_first

Class/interface/trait methods MUST BE ordered (accessors at the beginning of the class, ordered following properties order).

### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/ordered_with_getter_and_setter_first' => true,
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/ordered_with_getter_and_setter_first')
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
         }                                                                       //
     }                                                                           //
                                                                                 //
-    public function setFirstName($firstName)                                    //
+    public function getIdentifier()                                             //
     {                                                                           //
-        $this->firstName = $firstName;                                          //
+        return $this->identifier;                                               //
     }                                                                           //
                                                                                 //
-    public function setName($name)                                              //
+    public function getName()                                                   //
     {                                                                           //
-        $this->name = $name;                                                    //
+        return $this->name;                                                     //
     }                                                                           //
                                                                                 //
-    public function isEnabled()                                                 //
+    public function setName($name)                                              //
     {                                                                           //
-        return $this->enabled;                                                  //
+        $this->name = $name;                                                    //
     }                                                                           //
                                                                                 //
-    public function getName()                                                   //
+    public function getFirstName()                                              //
     {                                                                           //
-        return $this->name;                                                     //
+        return $this->firstName;                                                //
     }                                                                           //
                                                                                 //
-    public function getIdentifier()                                             //
+    public function setFirstName($firstName)                                    //
     {                                                                           //
-        return $this->identifier;                                               //
+        $this->firstName = $firstName;                                          //
     }                                                                           //
                                                                                 //
-    public function getFirstName()                                              //
+    public function isEnabled()                                                 //
     {                                                                           //
-        return $this->firstName;                                                //
+        return $this->enabled;                                                  //
     }                                                                           //
                                                                                 //
     public function enable()                                                    //
                                                                                 //
```

## PedroTroller/exceptions_punctuation

Exception messages MUST ends by ".", "…", "?" or "!".<br /><br /><i>Risky: will change the exception message.</i>

### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/exceptions_punctuation' => true,
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/exceptions_punctuation')
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
 class MyClass {                                                                 //
     public function fun1()                                                      //
     {                                                                           //
-        throw new \Exception('This is the message');                            //
+        throw new \Exception('This is the message.');                           //
     }                                                                           //
                                                                                 //
     public function fun2($data)                                                 //
     {                                                                           //
-        throw new LogicException(sprintf('This is the %s', 'message'));         //
+        throw new LogicException(sprintf('This is the %s.', 'message'));        //
     }                                                                           //
 }                                                                               //
                                                                                 //
```

## PedroTroller/forbidden_functions

Prohibited functions MUST BE commented on as prohibited


### Available options

 - `functions` (*optional*): The function names to be marked how prohibited
    - default: `var_dump`, `dump`, `die`

 - `comment` (*optional*): The prohibition message to put in the comment
    - default: `@TODO remove this line`

### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/forbidden_functions' => [ 'comment' => 'YOLO' ],
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/forbidden_functions', [ 'comment' => 'YOLO' ])
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
 class MyClass {                                                                 //
     public function fun()                                                       //
     {                                                                           //
-        var_dump('this is a var_dump');                                         //
+        var_dump('this is a var_dump'); // YOLO                                 //
                                                                                 //
         $this->dump($this);                                                     //
                                                                                 //
                                                                                 //
```
### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/forbidden_functions' => [ 'comment' => 'NEIN NEIN NEIN !!!', 'functions' => [ 'var_dump', 'var_export' ] ],
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/forbidden_functions', [ 'comment' => 'NEIN NEIN NEIN !!!', 'functions' => [ 'var_dump', 'var_export' ] ])
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
 class MyClass {                                                                 //
     public function fun()                                                       //
     {                                                                           //
-        var_dump('this is a var_dump');                                         //
+        var_dump('this is a var_dump'); // NEIN NEIN NEIN !!!                   //
                                                                                 //
         $this->dump($this);                                                     //
                                                                                 //
-        return var_export($this);                                               //
+        return var_export($this); // NEIN NEIN NEIN !!!                         //
     }                                                                           //
                                                                                 //
     public function dump($data)                                                 //
                                                                                 //
```

## PedroTroller/line_break_between_method_arguments

If the declaration of a method is too long, the arguments of this method MUST BE separated (one argument per line)


### Available options

 - `max-args` (*optional*): The maximum number of arguments allowed with splitting the arguments into several lines (use `false` to disable this feature)
    - default: `3`

 - `max-length` (*optional*): The maximum number of characters allowed with splitting the arguments into several lines
    - default: `120`

 - `automatic-argument-merge` (*optional*): If both conditions are met (the line is not too long and there are not too many arguments), then the arguments are put back inline
    - default: `true`

 - `inline-attributes` (*optional*): In the case of a split, the declaration of the attributes of the arguments of the method will be on the same line as the arguments themselves
    - default: `false`

### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/line_break_between_method_arguments' => [ 'max-args' => 4, 'max-length' => 120, 'automatic-argument-merge' => true, 'inline-attributes' => true ],
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/line_break_between_method_arguments', [ 'max-args' => 4, 'max-length' => 120, 'automatic-argument-merge' => true, 'inline-attributes' => true ])
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function fun2($arg1, array $arg2 = [], \ArrayAccess $arg3 = null, bool $bool = true, \Iterator $thisLastArgument = null)
-    {                                                                           //
+    public function fun2(                                                       //
+        $arg1,                                                                  //
+        array $arg2 = [],                                                       //
+        \ArrayAccess $arg3 = null,                                              //
+        bool $bool = true,                                                      //
+        \Iterator $thisLastArgument = null                                      //
+    ) {                                                                         //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function fun3(                                                       //
-        $arg1,                                                                  //
-        array $arg2 = []                                                        //
-    ) {                                                                         //
+    public function fun3($arg1, array $arg2 = [])                               //
+    {                                                                           //
         return;                                                                 //
     }                                                                           //
 }                                                                               //
                                                                                 //
```
### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/line_break_between_method_arguments' => [ 'max-args' => false, 'max-length' => 120, 'automatic-argument-merge' => true, 'inline-attributes' => true ],
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/line_break_between_method_arguments', [ 'max-args' => false, 'max-length' => 120, 'automatic-argument-merge' => true, 'inline-attributes' => true ])
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function fun2($arg1, array $arg2 = [], \ArrayAccess $arg3 = null, bool $bool = true, \Iterator $thisLastArgument = null)
-    {                                                                           //
+    public function fun2(                                                       //
+        $arg1,                                                                  //
+        array $arg2 = [],                                                       //
+        \ArrayAccess $arg3 = null,                                              //
+        bool $bool = true,                                                      //
+        \Iterator $thisLastArgument = null                                      //
+    ) {                                                                         //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function fun3(                                                       //
-        $arg1,                                                                  //
-        array $arg2 = []                                                        //
-    ) {                                                                         //
+    public function fun3($arg1, array $arg2 = [])                               //
+    {                                                                           //
         return;                                                                 //
     }                                                                           //
 }                                                                               //
                                                                                 //
```

## PedroTroller/line_break_between_statements

Each statement (in, for, foreach, ...) MUST BE separated by an empty line

### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/line_break_between_statements' => true,
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/line_break_between_statements')
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
         do {                                                                    //
             // ...                                                              //
         } while (true);                                                         //
+                                                                                //
         foreach (['foo', 'bar'] as $str) {                                      //
             // ...                                                              //
         }                                                                       //
+                                                                                //
         if (true === false) {                                                   //
             // ...                                                              //
         }                                                                       //
-                                                                                //
                                                                                 //
         while (true) {                                                          //
             // ...                                                              //
                                                                                 //
```

## PedroTroller/comment_line_to_phpdoc_block

Classy elements (method, property, ...) comments MUST BE a PhpDoc block

### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/comment_line_to_phpdoc_block' => true,
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/comment_line_to_phpdoc_block')
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
      */                                                                         //
     private $name;                                                              //
                                                                                 //
-    // @var string | null                                                       //
+    /**                                                                         //
+     * @var string | null                                                       //
+     */                                                                         //
     private $value;                                                             //
                                                                                 //
     /**                                                                         //
@@ @@                                                                            //
         $this->name = $name;                                                    //
     }                                                                           //
                                                                                 //
-    // Get the name                                                             //
-    //                                                                          //
-    // @return string                                                           //
+    /**                                                                         //
+     * Get the name                                                             //
+     *                                                                          //
+     * @return string                                                           //
+     */                                                                         //
     public function getName()                                                   //
     {                                                                           //
         return $this->name;                                                     //
     }                                                                           //
                                                                                 //
-    // Get the value                                                            //
-    // @return null | string                                                    //
+    /**                                                                         //
+     * Get the value                                                            //
+     * @return null | string                                                    //
+     */                                                                         //
     public function getValue()                                                  //
     {                                                                           //
         return $this->value;                                                    //
     }                                                                           //
                                                                                 //
-    // Set the value                                                            //
-                                                                                //
-    // @param string $value                                                     //
+    /**                                                                         //
+     * Set the value                                                            //
+     * @param string $value                                                     //
+     */                                                                         //
     public function setValue($value)                                            //
     {                                                                           //
         $this->value = $value;                                                  //
     }                                                                           //
 }                                                                               //
                                                                                 //
```

## PedroTroller/useless_code_after_return

All `return` that are not accessible (i.e. following another `return`) MUST BE deleted

### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/useless_code_after_return' => true,
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/useless_code_after_return')
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
      */                                                                         //
     public function fun1(Model\User $user, Model\Address $address = null) {     //
         return;                                                                 //
-                                                                                //
-        $user->setName('foo');                                                  //
-                                                                                //
-        return $this;                                                           //
     }                                                                           //
                                                                                 //
     /**                                                                         //
@@ @@                                                                            //
         switch ($this->status) {                                                //
             case 1:                                                             //
                 return $this->name;                                             //
-                break;                                                          //
             default:                                                            //
                 return $this;                                                   //
-                return $this;                                                   //
         }                                                                       //
     }                                                                           //
                                                                                 //
@@ @@                                                                            //
      */                                                                         //
     public function buildCallable()                                             //
     {                                                                           //
-        return function () { return true; return false; };                      //
+        return function () { return true; };                                    //
     }                                                                           //
 }                                                                               //
                                                                                 //
```

## PedroTroller/doctrine_migrations

Unnecessary empty methods (`getDescription()`, `up()`, `down()`) and comments MUST BE removed from Doctrine migrations


### Available options

 - `instanceof` (*optional*): The parent class of which Doctrine migrations extend
    - default: `Doctrine\Migrations\AbstractMigration`

### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/doctrine_migrations' => true,
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/doctrine_migrations')
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
 use Doctrine\DBAL\Schema\Schema;                                                //
 use Doctrine\Migrations\AbstractMigration;                                      //
                                                                                 //
-/**                                                                             //
- * Auto-generated Migration: Please modify to your needs!                       //
- */                                                                             //
 final class Version20190323095102 extends AbstractMigration                     //
 {                                                                               //
-    public function getDescription()                                            //
-    {                                                                           //
-        return '';                                                              //
-    }                                                                           //
                                                                                 //
     public function up(Schema $schema)                                          //
     {                                                                           //
-        // this up() migration is auto-generated, please modify it to your needs//
         $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
                                                                                 //
         $this->addSql('CREATE TABLE admin (identifier CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
@@ @@                                                                            //
                                                                                 //
     public function down(Schema $schema)                                        //
     {                                                                           //
-        // this down() migration is auto-generated, please modify it to your needs
         $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
                                                                                 //
         $this->addSql('DROP TABLE admin');                                      //
     }                                                                           //
 }                                                                               //
                                                                                 //
```
### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/doctrine_migrations' => [ 'instanceof' => [ 'Doctrine\Migrations\AbstractMigration' ] ],
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/doctrine_migrations', [ 'instanceof' => [ 'Doctrine\Migrations\AbstractMigration' ] ])
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
 use Doctrine\DBAL\Schema\Schema;                                                //
 use Doctrine\Migrations\AbstractMigration;                                      //
                                                                                 //
-/**                                                                             //
- * Auto-generated Migration: Please modify to your needs!                       //
- */                                                                             //
 final class Version20190323095102 extends AbstractMigration                     //
 {                                                                               //
-    public function getDescription()                                            //
-    {                                                                           //
-        return '';                                                              //
-    }                                                                           //
                                                                                 //
     public function up(Schema $schema)                                          //
     {                                                                           //
-        // this up() migration is auto-generated, please modify it to your needs//
         $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
                                                                                 //
         $this->addSql('CREATE TABLE admin (identifier CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
@@ @@                                                                            //
                                                                                 //
     public function down(Schema $schema)                                        //
     {                                                                           //
-        // this down() migration is auto-generated, please modify it to your needs
         $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
                                                                                 //
         $this->addSql('DROP TABLE admin');                                      //
     }                                                                           //
 }                                                                               //
                                                                                 //
```

## PedroTroller/phpspec

Phpspec scenario functions MUST NOT have a return type declaration.

Phpspec scenario functions MUST NOT have a scope.

The methods of the phpspec specification classes MUST BE sorted (let, letGo, its_*, it_*, getMatchers and the rest of the methods)

Lambda functions MUST NOT have a static scope.


### Available options

 - `instanceof` (*optional*): Parent classes of your spec classes.
    - default: `PhpSpec\ObjectBehavior`

### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/phpspec' => true,
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/phpspec')
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
 class TheSpec extends ObjectBehavior                                            //
 {                                                                               //
                                                                                 //
-    function letGo($file) {                                                     //
+    function let($file) {                                                       //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    private function thePrivateMethod() {                                       //
+    function letGo($file) {                                                     //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function itIsNotASpec($file) {                                       //
+    function it_is_a_spec($file) {                                              //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function it_is_a_spec($file) {                                       //
+    function its_other_function($file) {                                        //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function let($file) {                                                //
+    private function thePrivateMethod() {                                       //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function its_other_function($file) {                                 //
+    public function itIsNotASpec($file) {                                       //
         return;                                                                 //
     }                                                                           //
 }                                                                               //
                                                                                 //
```
### Configuration examples

```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    [
        // ...
        'PedroTroller/phpspec' => [ 'instanceof' => [ 'PhpSpec\ObjectBehavior' ] ],
        // ...
    ]
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```
**OR** using my [rule list builder](doc/rule-set-factory.md).
```php
// .php_cs.dist
<?php

$config = new PhpCsFixer\Config();
// ...
$config->setRules(
    PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/phpspec', [ 'instanceof' => [ 'PhpSpec\ObjectBehavior' ] ])
        ->getRules()
);
$config->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers());
// ...

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
 class TheSpec extends ObjectBehavior                                            //
 {                                                                               //
                                                                                 //
-    function letGo($file) {                                                     //
+    function let($file) {                                                       //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    private function thePrivateMethod() {                                       //
+    function letGo($file) {                                                     //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function itIsNotASpec($file) {                                       //
+    function it_is_a_spec($file) {                                              //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function it_is_a_spec($file) {                                       //
+    function its_other_function($file) {                                        //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function let($file) {                                                //
+    private function thePrivateMethod() {                                       //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function its_other_function($file) {                                 //
+    public function itIsNotASpec($file) {                                       //
         return;                                                                 //
     }                                                                           //
 }                                                                               //
                                                                                 //
```

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