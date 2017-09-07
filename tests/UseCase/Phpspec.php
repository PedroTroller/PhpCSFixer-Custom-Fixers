<?php

namespace tests\Usecase;

use PedroTroller\CS\Fixer\Phpspec\PhpspecFixer;
use PhpCsFixer\Fixer\FixerInterface;
use tests\UseCase;

class Phpspec implements UseCase
{
    public function getFixer(): FixerInterface
    {
        return new PhpspecFixer();
    }

    public function getRawScript(): string
    {
        return <<<SPEC
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{
    public function let(\$file) {
        return;
    }

    public function letGo(\$file) {
        return;
    }

    public function it_is_a_spec(\$file) {
        return;
    }

    public function itIsNotASpec(\$file) {
        return;
    }

    public function its_other_function(\$file) {
        return;
    }
}
SPEC;
    }

    public function getExpectation(): string
    {
        return <<<SPEC
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{
    function let(\$file) {
        return;
    }

    function letGo(\$file) {
        return;
    }

    function it_is_a_spec(\$file) {
        return;
    }

    public function itIsNotASpec(\$file) {
        return;
    }

    public function its_other_function(\$file) {
        return;
    }
}
SPEC;
    }
}
