<?php

namespace spec\PedroTroller\CS\Fixer\Contrib;

use PhpSpec\ObjectBehavior;

class VariableAssignAndReturnFixerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PedroTroller\CS\Fixer\Contrib\VariableAssignAndReturnFixer');
    }

    function it_returns_its_name()
    {
        $this->getName()->shouldReturn('variable_assign_and_return');
    }

    function it_fix_assign_and_return(\SplFileInfo $spl)
    {
        $class = <<<PHP
<?php

class TheClass
{
    public function theFunction()
    {
        // ...

        \$useless = 'yolo';

        return \$useless;
    }

    public function theOtherFunction()
    {
        // ...

        \$this->useless = FOO:bar();

        return \$this->useless;
    }

    public function theLastFunction()
    {
        // ...

        \$this->useless2 = true;

        return \$this->useless2;
    }
}
PHP;

        $expect = <<<PHP
<?php

class TheClass
{
    public function theFunction()
    {
        // ...

        return 'yolo';
    }

    public function theOtherFunction()
    {
        // ...

        \$this->useless = FOO:bar();

        return \$this->useless;
    }

    public function theLastFunction()
    {
        // ...

        \$this->useless2 = true;

        return \$this->useless2;
    }
}
PHP;

        $this->fix($spl, $class)->shouldReturn($expect);
    }
}
