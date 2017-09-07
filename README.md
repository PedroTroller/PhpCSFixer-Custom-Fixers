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
;

return $config;
```

### Fixes

```diff
--- Original
+++ New
@@ @@
         } while (true);
+
         foreach (['foo', 'bar'] as $str) {
             // ...
         }
+
         if (true === false) {
             // ...
         }
-
 
         while (true) {
             // ...
         }
     }
 }

```


## PedroTroller/single_line_comment

PHPSpec spec functions MUST NOT have a public scope.

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
;

return $config;
```

### Fixes

```diff
--- Original
+++ New
@@ @@
 {
-    /** @var string */
+    /**
+     * @var string
+     */
     private $prop1;
 
     /**
      * @var string
      */
     private $prop1;
 
     /**
      * @return null
      */
     public function fun1($file) {
         return;
     }
 
     /** @return null */
     public function fun2($file) {
         return;
     }
 }

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
;

return $config;
```

### Fixes

```diff
--- Original
+++ New
@@ @@
 
-    /**
-     * @var string
-     */
+    /** @var string */
     private $prop1;
 
     /**
      * @return null
      */
     public function fun1($file) {
         return;
     }
 
     /** @return null */
     public function fun2($file) {
         return;
     }
 }

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
;

return $config;
```

### Fixes

```diff
--- Original
+++ New
@@ @@
 
-    /**
-     * @var string
-     */
+    /** @var string */
     private $prop1;
 
-    /**
-     * @return null
-     */
+    /** @return null */
     public function fun1($file) {
         return;
     }
 
     /** @return null */
     public function fun2($file) {
         return;
     }
 }

```


## PedroTroller/phpspec

PHPSpec spec functions MUST NOT have a public scope.

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
;

return $config;
```

### Fixes

```diff
--- Original
+++ New
@@ @@
 {
-    public function let($file) {
+    function let($file) {
         return;
     }
 
-    public function letGo($file) {
+    function letGo($file) {
         return;
     }
 
-    public function it_is_a_spec($file) {
+    function it_is_a_spec($file) {
         return;
     }
 
     public function itIsNotASpec($file) {
         return;
     }
 
     public function its_other_function($file) {
         return;
     }
 }

```


# Contributions

## Run tests

```bash
composer tests
```
