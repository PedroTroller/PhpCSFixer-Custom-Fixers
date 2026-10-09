<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\BooleanOr\RepeatedOrEqualToInArrayRector;
use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnUnionTypeRector;
use Rector\TypeDeclaration\Rector\FunctionLike\AddParamTypeSplFixedArrayRector;
use Rector\Visibility\Rector\ClassMethod\ExplicitPublicClassMethodRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/bin',
        __DIR__.'/spec',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withRootFiles()
    // Targets the PHP version required by composer.json, kept in sync with PHP_MIN_VERSION by CI.
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
    )
    ->withSkip([
        // Adds a Tokens<Token> docblock on every method taking Tokens, which only repeats the native type.
        AddParamTypeSplFixedArrayRector::class,
        // PedroTroller/phpspec removes the visibility and the return type of spec methods.
        ExplicitPublicClassMethodRector::class      => [__DIR__.'/spec'],
        AddVoidReturnTypeWhereNoReturnRector::class => [__DIR__.'/spec'],
        // Infers float|int for token indexes computed from untyped parameters.
        ReturnUnionTypeRector::class,
        // Appends an unreachable return null after infinite loops and widens the return type.
        ExplicitReturnNullRector::class,
        // Turns null checks into in_array(), which PHPStan cannot narrow.
        RepeatedOrEqualToInArrayRector::class,
    ])
;
