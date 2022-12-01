<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use IteratorAggregate;
use PhpCsFixer\Fixer\FixerInterface;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Traversable;

/**
 * @implements IteratorAggregate<FixerInterface>
 */
final class Fixers implements IteratorAggregate
{
    public function getIterator(): Traversable
    {
        $finder = Finder::create()
            ->in(__DIR__)
            ->name('*.php')
        ;

        $files = array_map(
            fn ($file) => $file->getPathname(),
            iterator_to_array($finder)
        );

        sort($files);

        foreach ($files as $file) {
            /**
             * @var class-string<FixerInterface>
             */
            $className = str_replace('/', '\\', mb_substr($file, mb_strlen(__DIR__) - 21, -4));

            if (false === class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);

            if (false === $reflection->implementsInterface(FixerInterface::class)) {
                continue;
            }

            if ($reflection->isAbstract()) {
                continue;
            }

            yield new $className();
        }
    }
}
