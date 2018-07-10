<?php

namespace PedroTroller\CS\Fixer;

use ArrayIterator;
use IteratorAggregate;
use PhpCsFixer\Fixer\FixerInterface;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

final class Fixers implements IteratorAggregate
{
    public function getIterator()
    {
        $finder = new Finder();
        $finder
            ->in(__DIR__)
            ->name('*.php');
        $classes = [];

        $files = array_map(
            function ($file) {
                return $file->getPathname();
            },
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

            $classes[] = $class;
        }

        return new ArrayIterator(array_map(function ($class) { return new $class(); }, $classes));
    }
}
