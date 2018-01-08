<?php

namespace tests\UseCase;

use PedroTroller\CS\Fixer\Phpspec\OrderedSpecElementsFixer;
use PhpCsFixer\Fixer\FixerInterface;
use tests\UseCase;

class OrderedSpecElements implements UseCase
{
    public function getFixer(): FixerInterface
    {
        return new OrderedSpecElementsFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{
    function letGo($file) {
        return;
    }

    private function thePrivateMethod() {
        return;
    }

    public function itIsNotASpec($file) {
        return;
    }

    function it_is_a_spec($file) {
        return;
    }

    function let($file) {
        return;
    }

    public function its_other_function($file) {
        return;
    }
}
PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{

    function let($file) {
        return;
    }
    function letGo($file) {
        return;
    }

    function it_is_a_spec($file) {
        return;
    }

    private function thePrivateMethod() {
        return;
    }

    public function itIsNotASpec($file) {
        return;
    }

    public function its_other_function($file) {
        return;
    }
}
PHP;
    }
}
