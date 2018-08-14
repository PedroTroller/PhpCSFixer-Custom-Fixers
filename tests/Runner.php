<?php

declare(strict_types=1);

namespace tests;

use Exception;
use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Diff\Differ;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

final class Runner
{
    public static function run(): void
    {
        $deprecations = [];

        set_error_handler(
            function ($type, $message, $file, $line) use (&$deprecations): void {
                $deprecations[$message][] = sprintf('%s at line %d', $file, $line);
                $deprecations[$message] = array_unique($deprecations[$message]);

                sort($deprecations[$message]);
            },
            E_USER_DEPRECATED
        );

        echo "\n";

        self::runAnalyzerIntegrations();
        self::runUseCases();

        if (false === empty($deprecations)) {
            ksort($deprecations);

            $message = sprintf(
                "Deprecations : \n\n%s",
                implode(
                    "\n\n",
                    array_map(
                        function ($message, array $files) {
                            return sprintf("%s\n%s", $message, implode("\n", $files));
                        },
                        array_keys($deprecations),
                        $deprecations
                    )
                )
            );

            throw new Exception($message);
        }
    }

    private static function runUseCases(): void
    {
        $directory = sprintf('%s/UseCase', __DIR__);

        $finder = new Finder();
        $finder
            ->in($directory)
            ->name('*.php')
        ;

        foreach ($finder as $file) {
            $class = str_replace('/', '\\', mb_substr($file->getPathName(), mb_strlen(__DIR__) - 5, -4));

            if (false === class_exists($class)) {
                continue;
            }

            if (false === is_subclass_of($class, UseCase::class)) {
                continue;
            }

            $usecase = new $class();

            if ($usecase->getMinSupportedPhpVersion() > \PHP_VERSION_ID) {
                continue;
            }

            $fixer  = $usecase->getFixer();
            $tokens = Tokens::fromCode($usecase->getRawScript());

            $differ = new Differ();

            echo "#######################################################################################\n";
            echo "{$class}\n";
            echo "#######################################################################################\n";
            echo "\n";

            $fixer->fix(new SplFileInfo(__FILE__), $tokens);

            if ($usecase->getExpectation() !== $tokens->generateCode()) {
                throw new Exception($differ->diff($usecase->getExpectation(), $tokens->generateCode()));
            }
        }
    }

    private static function runAnalyzerIntegrations(): void
    {
        $directory = sprintf('%s/TokensAnalyzerIntegration', __DIR__);

        $finder = new Finder();
        $finder
            ->in($directory)
            ->name('*.php')
        ;

        foreach ($finder as $file) {
            $class = str_replace('/', '\\', mb_substr($file->getPathName(), mb_strlen(__DIR__) - 5, -4));

            if (false === class_exists($class)) {
                continue;
            }

            if (false === is_subclass_of($class, TokensAnalyzerIntegration::class)) {
                continue;
            }

            $integration = new $class();

            if ($integration->getMinSupportedPhpVersion() > \PHP_VERSION_ID) {
                continue;
            }

            $tokens = Tokens::fromCode($integration->getCode());

            echo "#######################################################################################\n";
            echo "{$class}\n";
            echo "#######################################################################################\n";
            echo "\n";

            $integration->assertions(new TokensAnalyzer($tokens), $tokens);
        }
    }
}
