<?php

namespace PedroTroller\CS\Fixer;

use IteratorAggregate;
use ArrayIterator;
use Symfony\Component\Finder\Finder;
use PhpCsFixer\Fixer\FixerInterface;
use ReflectionClass;

class Fixers implements IteratorAggregate
{
    public function getIterator()
    {
        $finder = new Finder();
        $finder
            ->in(__DIR__)
            ->name('*.php')
        ;
        $classes = [];

        foreach ($finder as $file) {
            $class = str_replace('/', '\\', substr($file->getPathname(), strlen(__DIR__) - 21, -4));

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

        return new ArrayIterator(array_map(function (string $class) { return new $class(); }, $classes));
    }
}
