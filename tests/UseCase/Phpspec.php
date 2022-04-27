<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\PhpspecFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use tests\UseCase;

final class Phpspec implements UseCase
{
    public function getFixers(): iterable
    {
        yield new VisibilityRequiredFixer();

        yield new VoidReturnFixer();

        yield new PhpspecFixer();
    }

    public function getRawScript(): string
    {
        return <<<'SPEC'
            <?php

            namespace spec\Project\TheNamespace;

            use PhpSpec\ObjectBehavior;

            class TheSpec extends ObjectBehavior
            {
                public function letGo($file) {
                    return;
                }

                public function foo()
                {
                    return 'bar';
                }

                public function it_is_a_spec($file) {
                    return;
                }

                public function it_is_a_spec_with_linebreaks_between_arguments(
                    $file
                ) {
                    return;
                }

                public function it_is_an_other_spec_with_linebreaks_between_arguments(
                    $file
                )
                {
                    return;
                }

                public function itIsNotASpec($file): void {
                    return;
                }

                public function getMatchers() {
                    return [];
                }

                public function let($file) {
                    return;
                }

                public function its_other_function_as_a_spec($file) {
                    return;
                }
            }
            SPEC;
    }

    public function getExpectation(): string
    {
        return <<<'SPEC'
            <?php

            namespace spec\Project\TheNamespace;

            use PhpSpec\ObjectBehavior;

            class TheSpec extends ObjectBehavior
            {

                function let($file)
                {
                    return;
                }
                function letGo($file)
                {
                    return;
                }

                function it_is_a_spec($file)
                {
                    return;
                }

                function it_is_a_spec_with_linebreaks_between_arguments(
                    $file
                ) {
                    return;
                }

                function it_is_an_other_spec_with_linebreaks_between_arguments(
                    $file
                ) {
                    return;
                }

                function its_other_function_as_a_spec($file)
                {
                    return;
                }

                public function getMatchers() {
                    return [];
                }

                public function foo()
                {
                    return 'bar';
                }

                public function itIsNotASpec($file): void {
                    return;
                }
            }
            SPEC;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
