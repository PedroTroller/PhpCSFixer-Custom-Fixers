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


## PedroTroller/ordered_with_getter_and_setter_first

Class/interface/trait methods MUST BE ordered (getter and setters at the end, ordered following arguments order).

### Configuration

```php
// .php_cs.dist
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

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/ordered_with_getter_and_setter_first')
        ->getRules()
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

## PedroTroller/exceptions_punctuation

Exception messages MUST ends by ".", "…", "?" or "!".<br /><br /><i>Risky: will change the exception message.</i>

### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/exceptions_punctuation' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/exceptions_punctuation')
        ->getRules()
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

Forbidden functions MUST BE commented

### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/forbidden_functions' => [ 'comment' => 'YOLO' ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/forbidden_functions', [ 'comment' => 'YOLO' ])
        ->getRules()
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
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/forbidden_functions' => [ 'comment' => 'NEIN NEIN NEIN !!!', 'functions' => [ 'var_dump', 'var_export' ] ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/forbidden_functions', [ 'comment' => 'NEIN NEIN NEIN !!!', 'functions' => [ 'var_dump', 'var_export' ] ])
        ->getRules()
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

## PedroTroller/line_break_between_method_arguments

Function methods MUST be splitted by a line break

### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/line_break_between_method_arguments' => [ 'max-args' => 4, 'max-length' => 120, 'automatic-argument-merge' => true ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/line_break_between_method_arguments', [ 'max-args' => 4, 'max-length' => 120, 'automatic-argument-merge' => true ])
        ->getRules()
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

Transform multiline docblocks with only one comment into a singleline docblock.

### Configuration

```php
// .php_cs.dist
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

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/line_break_between_statements')
        ->getRules()
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

## PedroTroller/comment_line_to_phpdoc_block

Classy elements (method, property, ...) comments MUST BE a PhpDoc block.

### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/comment_line_to_phpdoc_block' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/comment_line_to_phpdoc_block')
        ->getRules()
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

## PedroTroller/single_line_comment

Collapse/expand PHP single line comments

**DEPRECATED**
replaced by `single_line_comment_style`.

### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/single_line_comment' => [ 'action' => 'expanded' ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/single_line_comment', [ 'action' => 'expanded' ])
        ->getRules()
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/single_line_comment' => [ 'action' => 'collapsed' ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/single_line_comment', [ 'action' => 'collapsed' ])
        ->getRules()
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```


## PedroTroller/useless_comment

Remove useless comments regarding the method definition.

**DEPRECATED**
replaced by `no_superfluous_phpdoc_tags`.

### Configuration

```php
// .php_cs.dist
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

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/useless_comment')
        ->getRules()
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```


## PedroTroller/useless_code_after_return

Remove useless code after a returned value

### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/useless_code_after_return' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/useless_code_after_return')
        ->getRules()
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

Remove useless getDescription(), up(), down() and comments from Doctrine\Migrations\AbstractMigration if needed.

### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/doctrine_migrations' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/doctrine_migrations')
        ->getRules()
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
### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/doctrine_migrations' => [ 'instanceof' => [ 'Doctrine\Migrations\AbstractMigration' ] ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/doctrine_migrations', [ 'instanceof' => [ 'Doctrine\Migrations\AbstractMigration' ] ])
        ->getRules()
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

## PedroTroller/ordered_spec_elements

PHPSpec spec functions MUST BE ordered with specs first (order: let, letGo, its_* and it_* functons).

**DEPRECATED**
replaced by `PedroTroller/phpspec`.

### Configuration

```php
// .php_cs.dist
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

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/ordered_spec_elements')
        ->getRules()
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```


## PedroTroller/phpspec_scenario_return_type_declaration

PHPSpec spec functions MUST NOT have a return type declaration.

**DEPRECATED**
replaced by `PedroTroller/phpspec`.

### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/phpspec_scenario_return_type_declaration' => true,
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/phpspec_scenario_return_type_declaration')
        ->getRules()
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```


## PedroTroller/phpspec_scenario_scope

PHPSpec spec functions MUST NOT have a public scope.

**DEPRECATED**
replaced by `PedroTroller/phpspec`.

### Configuration

```php
// .php_cs.dist
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

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/phpspec_scenario_scope')
        ->getRules()
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```


## PedroTroller/phpspec



### Configuration

```php
// .php_cs.dist
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

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/phpspec')
        ->getRules()
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
### Configuration

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules([
        // ...
        'PedroTroller/phpspec' => [ 'instanceof' => [ 'PhpSpec\ObjectBehavior' ] ],
        // ...
    ])
    // ...
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;

return $config;
```

**OR** using my [rule list builder](doc/rule-set-factory.md).

```php
// .php_cs.dist
<?php

$config = PhpCsFixer\Config::create()
    // ...
    ->setRules(PedroTroller\CS\Fixer\RuleSetFactory::create()
        ->enable('PedroTroller/phpspec', [ 'instanceof' => [ 'PhpSpec\ObjectBehavior' ] ])
        ->getRules()
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
