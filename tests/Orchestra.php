<?php

declare(strict_types=1);

namespace tests;

use PedroTroller\CS\Fixer\ClassNotation\OrderedWithGetterAndSetterFirstFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\FixerInterface;
use Webmozart\Assert\Assert;

final class Orchestra
{
    /**
     * @var FixerInterface
     */
    private $fixer;

    private function __construct(FixerInterface $fixer)
    {
        $this->fixer = $fixer;
    }

    public static function run()
    {
        self::assert(new OrderedWithGetterAndSetterFirstFixer())
            ->before(new OrderedClassElementsFixer())
        ;
    }

    /**
     * @return Orchestra
     */
    public static function assert(FixerInterface $fixer)
    {
        return new self($fixer);
    }

    /**
     * @return Orchestra
     */
    public function before(FixerInterface $other)
    {
        echo sprintf("Run %s before %s\n", $this->fixer->getName(), $other->getName());

        Assert::greaterThan(
            $this->fixer->getPriority(),
            $other->getPriority()
        );

        return $this;
    }

    /**
     * @return Orchestra
     */
    public function after(FixerInterface $other)
    {
        echo sprintf("Run %s after %s\n", $this->fixer->getName(), $other->getName());

        Assert::lessThan(
            $this->fixer->getPriority(),
            $other->getPriority()
        );

        return $this;
    }
}
