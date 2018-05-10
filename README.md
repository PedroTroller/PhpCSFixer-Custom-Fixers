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


## PedroTroller/line_break_between_method_arguments

Function methods MUST be splitted by a line break

### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/line_break_between_method_arguments' => [ "max-args" => 4, "max-length" => 120 ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

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

Transform multiline docblocks with only one comment into a singleline docblock.

### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/line_break_between_statements' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

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


## PedroTroller/forbidden_functions

Forbidden functions MUST BE commented

### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/forbidden_functions' => [ "comment" => "YOLO" ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

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
### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/forbidden_functions' => [ "comment" => "NEIN NEIN NEIN !!!", "functions" => [ "var_dump", "var_export" ] ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

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


## PedroTroller/useless_comment

Remove useless comments regarding the method definition

### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/useless_comment' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
                                                                                 //
 class TheClass                                                                  //
 {                                                                               //
-    /**                                                                         //
-     * @param Model\User $user                                                  //
-     */                                                                         //
     public function fun1(Model\User $user, Model\Address $address = null) {     //
         return;                                                                 //
     }                                                                           //
                                                                                 //
```


## PedroTroller/single_line_comment

Collapse/expand PHP single line comments

### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/single_line_comment' => [ "action" => "expanded" ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
                                                                                 //
 class TheClass                                                                  //
 {                                                                               //
-    /** @var string */                                                          //
+    /**                                                                         //
+     * @var string                                                              //
+     */                                                                         //
     private $prop1;                                                             //
                                                                                 //
     /**                                                                         //
@@ @@                                                                            //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    /** @return null */                                                         //
+    /**                                                                         //
+     * @return null                                                             //
+     */                                                                         //
     public function fun2($file) {                                               //
         return;                                                                 //
     }                                                                           //
 }                                                                               //
                                                                                 //
```
### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/single_line_comment' => [ "action" => "collapsed" ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
     /** @var string */                                                          //
     private $prop1;                                                             //
                                                                                 //
-    /**                                                                         //
-     * @var string                                                              //
-     */                                                                         //
+    /** @var string */                                                          //
     private $prop1;                                                             //
                                                                                 //
-    /**                                                                         //
-     * @return null                                                             //
-     */                                                                         //
+    /** @return null */                                                         //
     public function fun1($file) {                                               //
         return;                                                                 //
     }                                                                           //
                                                                                 //
```
### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/single_line_comment' => [ "action" => "collapsed", "types" => [ "@var", "@return" ] ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
     /** @var string */                                                          //
     private $prop1;                                                             //
                                                                                 //
-    /**                                                                         //
-     * @var string                                                              //
-     */                                                                         //
+    /** @var string */                                                          //
     private $prop1;                                                             //
                                                                                 //
-    /**                                                                         //
-     * @return null                                                             //
-     */                                                                         //
+    /** @return null */                                                         //
     public function fun1($file) {                                               //
         return;                                                                 //
     }                                                                           //
                                                                                 //
```


## PedroTroller/ordered_with_getter_and_setter_first

Class/interface/trait methods MUST BE ordered (getter and setters at the end, ordered following arguments order).

### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/ordered_with_getter_and_setter_first' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

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


## PedroTroller/phpspec

PHPSpec spec functions MUST NOT have a public scope AND PHPSpec spec functions MUST BE ordered with specs first (order: let, letGo, its_* and it_* functons).

### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/phpspec' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

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
-    public function letGo($file) {                                              //
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


## PedroTroller/ordered_spec_elements

PHPSpec spec functions MUST BE ordered with specs first (order: let, letGo, its_* and it_* functons).

### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/ordered_spec_elements' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

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
-    function it_is_a_spec($file) {                                              //
+    public function its_other_function($file) {                                 //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    function let($file) {                                                       //
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


## PedroTroller/phpspec_scenario_scope

PHPSpec spec functions MUST NOT have a public scope.

### Configuration

```php
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/phpspec_scenario_scope' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

### Fixes

```diff
--- Original                                                                     // 80 chars
+++ New                                                                          //
@@ @@                                                                            //
                                                                                 //
 class TheSpec extends ObjectBehavior                                            //
 {                                                                               //
-    public function let($file) {                                                //
+    function let($file) {                                                       //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function letGo($file) {                                              //
+    function letGo($file) {                                                     //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function it_is_a_spec($file) {                                       //
+    function it_is_a_spec($file) {                                              //
         return;                                                                 //
     }                                                                           //
                                                                                 //
@@ @@                                                                            //
         return;                                                                 //
     }                                                                           //
                                                                                 //
-    public function its_other_function($file) {                                 //
+    function its_other_function($file) {                                        //
         return;                                                                 //
     }                                                                           //
 }                                                                               //
                                                                                 //
```


# Contributions

## Run tests

```bash
composer tests
```
