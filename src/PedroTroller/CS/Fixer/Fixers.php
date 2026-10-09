<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use Generator;
use IteratorAggregate;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

/**
 * @implements IteratorAggregate<int, AbstractFixer>
 */
final class Fixers implements IteratorAggregate
{
    /**
     * @return Generator<int, AbstractFixer>
     */
    public function getIterator(): Generator
    {
        $finder = Finder::create()
            ->in(__DIR__)
            ->name('*.php')
        ;

        $files = array_map(
            static fn ($file) => $file->getPathname(),
            iterator_to_array($finder)
        );

        sort($files);

        foreach ($files as $file) {
            $class = str_replace('/', '\\', mb_substr($file, mb_strlen(__DIR__) - 21, -4));

            if (false === class_exists($class)) {
                continue;
            }

            $rfl = new ReflectionClass($class);

            if (false === $rfl->isSubclassOf(AbstractFixer::class)) {
                continue;
            }

            if ($rfl->isAbstract()) {
                continue;
            }

            yield new $class();
        }
    }
}
