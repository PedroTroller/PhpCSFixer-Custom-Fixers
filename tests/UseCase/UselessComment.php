<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\Comment\UselessCommentFixer;
use tests\UseCase;

final class UselessComment implements UseCase
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
     * @var User
     */
    private $user;

    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
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
     * @var User
     */
    private $user;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }
}
PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
