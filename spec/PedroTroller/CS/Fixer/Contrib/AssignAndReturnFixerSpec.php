<?php

namespace spec\PedroTroller\CS\Fixer\Contrib;

use PhpSpec\ObjectBehavior;

class AssignAndReturnFixerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PedroTroller\CS\Fixer\Contrib\AssignAndReturnFixer');
    }

    function it_returns_its_name()
    {
        $this->getName()->shouldReturn('assign_and_return');
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

        return \$this->useless = FOO:bar();
    }

    public function theLastFunction()
    {
        // ...

        return \$this->useless2 = true;
    }
}
PHP;

        $this->fix($spl, $class)->shouldReturn($expect);
    }

    function it_supports_functions_with_default_argument(\SplFileInfo $spl)
    {
        $class = <<<PHP
<?php

class TheClass
{
    /**
     * {@inheritdoc}
     */
    public function start(Request \$request, AuthenticationException \$authException = null)
    {
    }
}
PHP;

        $expect = <<<PHP
<?php

class TheClass
{
    /**
     * {@inheritdoc}
     */
    public function start(Request \$request, AuthenticationException \$authException = null)
    {
    }
}
PHP;

        $this->fix($spl, $class)->shouldReturn($expect);
    }
}
