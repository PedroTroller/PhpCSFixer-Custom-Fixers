<?php

declare(strict_types=1);

namespace tests;

use RuntimeException;

final class Fixture
{
    public static function read(string $path): string
    {
        $content = file_get_contents($path);

        if (false === $content) {
            throw new RuntimeException(\sprintf('Unable to read fixture %s.', $path));
        }

        return $content;
    }
}
