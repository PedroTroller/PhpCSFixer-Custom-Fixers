<?php

namespace spec\PedroTroller\CS\Fixer\Contrib;

use PhpSpec\ObjectBehavior;

class LineBreakBetweenStatementsFixerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PedroTroller\CS\Fixer\Contrib\LineBreakBetweenStatementsFixer');
    }

    function it_returns_its_name()
    {
        $this->getName()->shouldReturn('line_break_between_statements');
    }

    function it_fix_userless_or_missing_blank_lines(\SplFileInfo $spl)
    {
        $class = <<<PHP
<?php

class TheClass
{
    public function theFunction()
    {
        if (true) {
            return;
        }
        foreach ([] as \$nothing) {
            continue;
        }




        while(\$forever = true) {

        }
    }
}
PHP;

        $expect = <<<PHP
<?php

class TheClass
{
    public function theFunction()
    {
        if (true) {
            return;
        }

        foreach ([] as \$nothing) {
            continue;
        }

        while(\$forever = true) {

        }
    }
}
PHP;

        $this->fix($spl, $class)->shouldReturn($expect);
    }
}
