<?php

declare(strict_types=1);

namespace tests\UseCase\SingleLineComment;

use PedroTroller\CS\Fixer\Comment\SingleLineCommentFixer;
use tests\UseCase;

final class Collapsed implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new SingleLineCommentFixer();

        $fixer->configure([
            'action' => 'collapsed',
            'types'  => ['@var'],
        ]);

        yield $fixer;
    }

    public function getRawScript(): string
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

    public function getExpectation(): string
    {
        return <<<'PHP'
<?php

namespace Project\TheNamespace;

class TheClass
{
    /** @var string */
    private $prop1;

    /** @var string */
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

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
