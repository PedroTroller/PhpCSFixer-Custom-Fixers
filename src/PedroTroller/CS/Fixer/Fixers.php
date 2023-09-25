<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use Generator;
use IteratorAggregate;
use PhpCsFixer\Fixer\FixerInterface;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

final class Fixers implements IteratorAggregate
{
    /**
     * {@inheritdoc}
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

            if (false === $rfl->implementsInterface(FixerInterface::class)) {
                continue;
            }

            if ($rfl->isAbstract()) {
                continue;
            }

            yield new $class();
        }
    }
}
