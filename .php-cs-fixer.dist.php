<?php

declare(strict_types=1);

use PedroTroller\CS\Fixer\Fixers;
use PedroTroller\CS\Fixer\RuleSetFactory;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules(
        RuleSetFactory::create()
            ->phpCsFixer(true)
            ->php(7.3, true)
            ->pedrotroller(true)
            ->enable('ordered_imports')
            ->enable('ordered_interfaces')
            ->enable('align_multiline_comment')
            ->enable('array_indentation')
            ->enable('no_superfluous_phpdoc_tags')
            ->enable('simplified_null_return')
            ->enable('binary_operator_spaces', [
                'operators' => [
                    '='  => 'align_single_space_minimal',
                    '=>' => 'align_single_space_minimal',
                ],
            ])
            ->disable('simplified_null_return')
            ->getRules()
    )
    ->setUsingCache(false)
    ->registerCustomFixers(new Fixers())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->append([__FILE__, __DIR__.'/bin/doc'])
    )
;
