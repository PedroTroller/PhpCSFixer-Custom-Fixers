<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\Phpspec\PhpspecScenarioScopeFixer;
use tests\UseCase;

final class PhpspecScenarioScope implements UseCase
{
    public function getFixers(): iterable
    {
        yield new PhpspecScenarioScopeFixer();
    }

    public function getRawScript(): string
    {
        return <<<'SPEC'
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{
    public function let($file) {
        return;
    }

    public function letGo($file) {
        return;
    }

    public function it_is_a_spec($file) {
        return;
    }

    public function itIsNotASpec($file) {
        return;
    }

    public function its_other_function_as_a_spec($file) {
        return;
    }
}
SPEC;
    }

    public function getExpectation(): string
    {
        return <<<'SPEC'
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

    public function itIsNotASpec($file) {
        return;
    }

    function its_other_function_as_a_spec($file) {
        return;
    }
}
SPEC;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
