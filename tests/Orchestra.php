<?php

declare(strict_types=1);

namespace tests;

use PedroTroller\CS\Fixer\ClassNotation\OrderedWithGetterAndSetterFirstFixer;
use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use PedroTroller\CS\Fixer\DoctrineMigrationsFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use Webmozart\Assert\Assert;

final readonly class Orchestra
{
    private function __construct(private FixerInterface $fixer) {}

    public static function run(): void
    {
        self::assert(new OrderedWithGetterAndSetterFirstFixer())
            ->before(new OrderedClassElementsFixer())
        ;

        self::assert(new DoctrineMigrationsFixer())
            ->before(new ClassAttributesSeparationFixer())
            ->before(new NoEmptyPhpdocFixer())
            ->before(new NoExtraBlankLinesFixer())
            ->before(new SingleLineAfterImportsFixer())
            ->before(new NoWhitespaceInBlankLineFixer())
        ;

        self::assert(new LineBreakBetweenMethodArgumentsFixer())
            ->after(new BracesFixer())
            ->after(new MethodArgumentSpaceFixer())
        ;

        echo "\n";
    }

    public static function assert(FixerInterface $fixer): self
    {
        return new self($fixer);
    }

    public function before(FixerInterface $other): self
    {
        echo \sprintf("\nRun %s before %s\n", $this->fixer->getName(), $other->getName());

        Assert::greaterThan(
            $this->fixer->getPriority(),
            $other->getPriority()
        );

        return $this;
    }

    public function after(FixerInterface $other): self
    {
        echo \sprintf("\nRun %s after %s\n", $this->fixer->getName(), $other->getName());

        Assert::lessThan(
            $this->fixer->getPriority(),
            $other->getPriority()
        );

        return $this;
    }
}
