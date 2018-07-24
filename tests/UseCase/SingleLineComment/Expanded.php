<?php

declare(strict_types=1);

namespace tests\UseCase\SingleLineComment;

use PedroTroller\CS\Fixer\Comment\SingleLineCommentFixer;
use tests\UseCase;

final class Expanded implements UseCase
{
    // {@inheritdoc}
    public function getFixer()
    {
        $fixer = new SingleLineCommentFixer();
        $fixer->configure([
            'action' => 'expanded',
            'types'  => ['@var'],
        ]);

        return $fixer;
    }

    // {@inheritdoc}
    public function getRawScript()
    {
        return <<<'PHP'
<?php

namespace Project\TheNamespace;

class TheClass
{
    /** @var string */
    private $prop1;

    /**
     * @var string
     */
    private $prop1;

    /**
     * @return null
     */
    public function fun1($file) {
        return;
    }

    /** @return null */
    public function fun2($file) {
        return;
    }
}
PHP;
    }

    // {@inheritdoc}
    public function getExpectation()
    {
        return <<<'PHP'
<?php

namespace Project\TheNamespace;

class TheClass
{
    /**
     * @var string
     */
    private $prop1;

    /**
     * @var string
     */
    private $prop1;

    /**
     * @return null
     */
    public function fun1($file) {
        return;
    }

    /** @return null */
    public function fun2($file) {
        return;
    }
}
PHP;
    }

    // {@inheritdoc}
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }
}
