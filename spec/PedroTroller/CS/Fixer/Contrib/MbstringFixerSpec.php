<?php

namespace spec\PedroTroller\CS\Fixer\Contrib;

use PhpSpec\ObjectBehavior;

class MbstringFixerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PedroTroller\CS\Fixer\Contrib\MbstringFixer');
    }

    function it_returns_its_name()
    {
        $this->getName()->shouldReturn('mbstring');
    }

    function it_replace_functions(\SplFileInfo $spl)
    {
        $class = <<<PHP
<?php

class TheClass
{
    public function toLower()
    {
        return strtolower('preg_replace');
    }

    public function toUpper()
    {
        return strtolower('substr');
    }

    public function getFirst()
    {
        return substr('substr', 0, 1);
    }
}
PHP;

        $expect = <<<PHP
<?php

class TheClass
{
    public function toLower()
    {
        return mb_strtolower('preg_replace');
    }

    public function toUpper()
    {
        return mb_strtolower('substr');
    }

    public function getFirst()
    {
        return mb_substr('substr', 0, 1);
    }
}
PHP;

        $this->fix($spl, $class)->shouldReturn($expect);
    }
}
