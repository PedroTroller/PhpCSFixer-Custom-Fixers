<?php

declare(strict_types=1);

use PedroTroller\CS\Fixer\Fixers;
use PedroTroller\CS\Fixer\RuleSetFactory;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules(
        RuleSetFactory::create()
            ->phpCsFixer(true)
            ->php(8.2, true)
            ->pedrotroller(true)
            ->enable('align_multiline_comment')
            ->enable('array_indentation')
            ->enable('binary_operator_spaces', [
                'operators' => [
                    '='  => 'align_single_space_minimal',
                    '=>' => 'align_single_space_minimal',
                ],
            ])
            ->enable('global_namespace_import', [
                'import_classes' => true, 'import_constants' => false, 'import_functions' => false,
            ])
            ->enable('no_superfluous_phpdoc_tags')
            ->enable('ordered_imports')
            ->enable('ordered_interfaces')
            ->getRules()
    )
    ->setUsingCache(false)
    ->setUnsupportedPhpVersionAllowed(true)
    ->registerCustomFixers(new Fixers())
    ->setFinder(
        Finder::create()
            ->in(__DIR__)
            ->append([__FILE__, __DIR__.'/bin/doc'])
    )
;
