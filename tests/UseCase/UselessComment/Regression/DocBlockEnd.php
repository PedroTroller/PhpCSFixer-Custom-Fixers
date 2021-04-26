<?php

declare(strict_types=1);

namespace tests\UseCase\UselessComment\Regression;

use PedroTroller\CS\Fixer\Comment\UselessCommentFixer;
use tests\UseCase;

final class DocBlockEnd implements UseCase
{
    public function getFixers(): iterable
    {
        yield new UselessCommentFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace App;

final class TheClass
{
    /**
     * @param string $attribute
     * @param mixed  $subject
     * @param User   $user
     *
     * @return bool
     */
    public function canUnPublish(string $attribute, $subject, User $user): bool
    {
        return $this->canPublish($attribute, $subject, $user);
    }

    /**
     * @param string $attribute
     * @param mixed  $subject
     * @param User   $user
     *
     * @return bool
     */
    public function canPublish(string $attribute, $subject, User $user): bool
    {
        return $this->canEdit($attribute, $subject, $user);
    }
}
PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace App;

final class TheClass
{
    /**
     * @param mixed  $subject
     */
    public function canUnPublish(string $attribute, $subject, User $user): bool
    {
        return $this->canPublish($attribute, $subject, $user);
    }

    /**
     * @param mixed  $subject
     */
    public function canPublish(string $attribute, $subject, User $user): bool
    {
        return $this->canEdit($attribute, $subject, $user);
    }
}
PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 70100;
    }
}
