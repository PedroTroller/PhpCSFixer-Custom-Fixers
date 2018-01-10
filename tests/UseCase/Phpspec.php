<?php

namespace tests\Usecase;

use PedroTroller\CS\Fixer\Phpspec\PhpspecFixer;
use tests\UseCase;

class Phpspec implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        return new PhpspecFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
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

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
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

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }
}
