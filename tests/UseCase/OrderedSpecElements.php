<?php

namespace tests\UseCase;

use PedroTroller\CS\Fixer\Phpspec\OrderedSpecElementsFixer;
use tests\UseCase;

class OrderedSpecElements implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        return new OrderedSpecElementsFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
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

    function its_other_function_as_a_spec($file) {
        return;
    }

    public function getMatchers()
    {
        return [];
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
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

    function its_other_function_as_a_spec($file) {
        return;
    }

    public function getMatchers()
    {
        return [];
    }

    private function thePrivateMethod() {
        return;
    }

    public function itIsNotASpec($file) {
        return;
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }
}
