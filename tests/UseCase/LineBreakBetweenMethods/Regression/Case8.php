<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

/**
 * https://github.com/PedroTroller/PhpCSFixer-Custom-Fixers/issues/169.
 */
final class Case8 implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'inline-attributes' => true,
        ]);

        yield $fixer;
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            use Doctrine\Orm\Mapping as ORM;

            class Foo {
                public function __construct(
                    #[ORM\Id]
                    #[ORM\Column(type: 'uuid')]
                    private readonly UuidInterface $id,
                    #[ORM\ManyToOne]
                    #[ORM\JoinColumn(nullable: false)]
                    private readonly AuthUser $authUser,
                    #[ORM\Column(length: 40, nullable: false)]
                    private readonly string $accessToken,
                    #[ORM\Column(length: 256, nullable: false)]
                    private readonly string $refreshToken,
                    #[ORM\Column(length: 255, nullable: false)]
                    private readonly string $deviceUserAgent,
                    #[ORM\Column(length: 100)]
                    private readonly string $deviceType,
                    #[ORM\Column(length: 100)]
                    private readonly string $deviceOs,
                    #[ORM\Column(length: 100)]
                    private readonly string $deviceBrowser
                ) {

                }
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
            <?php

            use Doctrine\Orm\Mapping as ORM;

            class Foo {
                public function __construct(
                    #[ORM\Id] #[ORM\Column(type: 'uuid')] private readonly UuidInterface $id,
                    #[ORM\ManyToOne] #[ORM\JoinColumn(nullable: false)] private readonly AuthUser $authUser,
                    #[ORM\Column(length: 40, nullable: false)] private readonly string $accessToken,
                    #[ORM\Column(length: 256, nullable: false)] private readonly string $refreshToken,
                    #[ORM\Column(length: 255, nullable: false)] private readonly string $deviceUserAgent,
                    #[ORM\Column(length: 100)] private readonly string $deviceType,
                    #[ORM\Column(length: 100)] private readonly string $deviceOs,
                    #[ORM\Column(length: 100)] private readonly string $deviceBrowser
                ) {

                }
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 80100;
    }
}
