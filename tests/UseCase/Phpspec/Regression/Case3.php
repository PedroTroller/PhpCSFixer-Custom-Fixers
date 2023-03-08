<?php

declare(strict_types=1);

namespace tests\UseCase\Phpspec\Regression;

use PedroTroller\CS\Fixer\PhpspecFixer;
use tests\UseCase;

final class Case3 implements UseCase
{
    public function getFixers(): iterable
    {
        yield new PhpspecFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            declare(strict_types=1);

            namespace spec\App;

            use PhpSpec\ObjectBehavior;

            class MyClass extends ObjectBehavior
            {
                function it_is_a_scenario(FileManager $fileManager)
                {
                    $fileManager
                        ->createTemporaryFile(Argument::any(), Argument::any())
                        ->will(
                            static fn ($args) => tempnam($args[1] ?? sys_get_temp_dir(), $args[0] ?? uniqid())
                        )
                    ;

                    $fileManager
                        ->createTemporaryFile(Argument::any(), Argument::any())
                        ->will(
                            static function ($args) {
                                return tempnam($args[1] ?? sys_get_temp_dir(), $args[0] ?? uniqid());
                            }
                        )
                    ;
                }

                private static function staticFunc(): string
                {
                    return 'Hello';
                }
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
            <?php

            declare(strict_types=1);

            namespace spec\App;

            use PhpSpec\ObjectBehavior;

            class MyClass extends ObjectBehavior
            {
                function it_is_a_scenario(FileManager $fileManager)
                {
                    $fileManager
                        ->createTemporaryFile(Argument::any(), Argument::any())
                        ->will(
                            fn ($args) => tempnam($args[1] ?? sys_get_temp_dir(), $args[0] ?? uniqid())
                        )
                    ;

                    $fileManager
                        ->createTemporaryFile(Argument::any(), Argument::any())
                        ->will(
                            function ($args) {
                                return tempnam($args[1] ?? sys_get_temp_dir(), $args[0] ?? uniqid());
                            }
                        )
                    ;
                }

                private static function staticFunc(): string
                {
                    return 'Hello';
                }
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
