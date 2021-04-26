<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\Phpspec\OrderedSpecElementsFixer;
use tests\UseCase;

final class OrderedSpecElements implements UseCase
{
    public function getFixers(): iterable
    {
        yield new OrderedSpecElementsFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{
    /**
     * @var string
     */
    private $file;

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

    public function getExpectation(): string
    {
        return <<<'PHP'
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{
    /**
     * @var string
     */
    private $file;

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

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
