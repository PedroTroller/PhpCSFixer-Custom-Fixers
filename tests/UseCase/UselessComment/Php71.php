<?php

declare(strict_types=1);

namespace tests\UseCase\UselessComment;

use PedroTroller\CS\Fixer\Comment\UselessCommentFixer;
use tests\UseCase;

final class Php71 implements UseCase
{
    public function getFixers(): iterable
    {
        yield new UselessCommentFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
<?php

class MyClass {

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
<?php

class MyClass {

    /**
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 70100;
    }
}
