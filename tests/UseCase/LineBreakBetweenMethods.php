<?php

namespace tests\UseCase;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use PhpCsFixer\Fixer\FixerInterface;
use tests\UseCase;

class LineBreakBetweenMethods implements UseCase
{
    public function getFixer(): FixerInterface
    {
        return new LineBreakBetweenMethodArgumentsFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
<?php

namespace Project\TheNamespace;

class TheClass
{
    public function fun1($arg1, array $arg2 = [], \ArrayAccess $arg3 = null, $foo = 'bar')
    {
        return;
    }

    public function fun2($arg1, array $arg2 = []) {
        return;
    }

    public function fun3() {

    }

    public function fun4(
        $foo,
        $bar,
        $bar,
        $boolean = true,
        $integer = 1,
        $string = 'string'
    ) {

    }
}
PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
<?php

namespace Project\TheNamespace;

class TheClass
{
    public function fun1(
        $arg1,
        array $arg2 = [],
        \ArrayAccess $arg3 = null,
        $foo = 'bar'
    ) {
        return;
    }

    public function fun2($arg1, array $arg2 = []) {
        return;
    }

    public function fun3() {

    }

    public function fun4(
        $foo,
        $bar,
        $bar,
        $boolean = true,
        $integer = 1,
        $string = 'string'
    ) {

    }
}
PHP;
    }
}
