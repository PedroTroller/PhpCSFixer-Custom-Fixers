<?php

namespace spec\PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\Contrib\SingleCommentCollapsedFixer;
use PhpSpec\ObjectBehavior;

class SingleCommentCollapsedFixerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PedroTroller\CS\Fixer\Contrib\SingleCommentCollapsedFixer');
    }

    function it_returns_its_name()
    {
        $this->getName()->shouldReturn('single_comment_collapsed');
    }

    function it_fix_spec_file(\SplFileInfo $spl)
    {
        $spec = <<<SPEC
<?php

namespace Project\Namespace;

class TheClass
{
    /**
     * @var string
     */
    private \$string;

    /** @var string */
    private \$yolo;

    /**
     * @param string \$string
     *
     * @return TheClass
     */
    function theFunction(\$string) {
        return \$this;
    }

    /**
     * @param string \$string
     */
    function theOtherFunction(\$string) {
    }
}
SPEC;

        $expect = <<<SPEC
<?php

namespace Project\Namespace;

class TheClass
{
    /** @var string */
    private \$string;

    /** @var string */
    private \$yolo;

    /**
     * @param string \$string
     *
     * @return TheClass
     */
    function theFunction(\$string) {
        return \$this;
    }

    /** @param string \$string */
    function theOtherFunction(\$string) {
    }
}
SPEC;

        SingleCommentCollapsedFixer::setCollapsedComments(array('@var', '@param'));
        $spl->getExtension()->willReturn('php');

        $this->fix($spl, $spec)->shouldReturn($expect);
    }
}
