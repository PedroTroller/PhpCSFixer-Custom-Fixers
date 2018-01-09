<?php

namespace tests;

use Exception;
use PhpCsFixer\Tokenizer\Tokens;
use SebastianBergmann\Diff\Differ;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class Runner
{
    public static function run()
    {
        $directory = sprintf('%s/UseCase', __DIR__);

        $finder = new Finder();
        $finder
            ->in($directory)
            ->name('*.php');

        echo "\n";

        foreach ($finder as $file) {
            $class = str_replace('/', '\\', mb_substr($file->getPathName(), mb_strlen(__DIR__) - 5, -4));

            if (false === class_exists($class)) {
                continue;
            }

            if (false === is_subclass_of($class, UseCase::class)) {
                continue;
            }

            $usecase = new $class();

            if ($usecase->getMinSupportedPhpVersion() > PHP_VERSION_ID) {
                continue;
            }

            $fixer  = $usecase->getFixer();
            $tokens = Tokens::fromCode($usecase->getRawScript());

            $differ = new Differ();

            $fixer->fix(new SplFileInfo(__FILE__), $tokens);

            echo "#######################################################################################\n";
            echo "{$class}\n";
            echo "#######################################################################################\n";
            echo "\n";

            if ($usecase->getExpectation() !== $tokens->generateCode()) {
                throw new Exception($differ->diff($usecase->getExpectation(), $tokens->generateCode()));
            }
        }
    }
}
