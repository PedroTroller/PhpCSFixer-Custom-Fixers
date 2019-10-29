<?php

declare(strict_types=1);

namespace spec\PedroTroller\CS\Fixer;

use PedroTroller\CS\Fixer\Fixers;
use PedroTroller\CS\Fixer\RuleSetFactory;
use PhpSpec\ObjectBehavior;

final class RuleSetFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RuleSetFactory::class);
    }

    function it_adds_a_psr0_set()
    {
        $this->psr0()->getRules()->shouldReturn(['@psr0' => true]);
    }

    function it_adds_a_psr1_set()
    {
        $this->psr1()->getRules()->shouldReturn(['@psr1' => true]);
    }

    function it_adds_a_psr2_set()
    {
        $this->psr2()->getRules()->shouldReturn(['@psr2' => true]);
    }

    function it_adds_a_psr4_set()
    {
        $this->psr4()->getRules()->shouldReturn(['@psr4' => true]);
    }

    function it_adds_a_symfony_set()
    {
        $this->symfony()->getRules()->shouldReturn(['@Symfony' => true]);
    }

    function it_adds_a_phpCsFixer_set()
    {
        $this->phpCsFixer()->getRules()->shouldReturn(['@PhpCsFixer' => true]);
    }

    function it_adds_a_doctrine_annotation_set()
    {
        $this->doctrineAnnotation()->getRules()->shouldReturn(['@DoctrineAnnotation' => true]);
    }

    function it_adds_a_symfony_strict_set()
    {
        $this->symfony()->getRules()->shouldReturn([
            '@Symfony' => true,
        ]);

        $this->symfony(true)->getRules()->shouldReturn([
            '@Symfony'       => true,
            '@Symfony:risky' => true,
        ]);
    }

    function it_adds_a_phpCsFixer_strict_set()
    {
        $this->phpCsFixer()->getRules()->shouldReturn([
            '@PhpCsFixer' => true,
        ]);

        $this->phpCsFixer(true)->getRules()->shouldReturn([
            '@PhpCsFixer'       => true,
            '@PhpCsFixer:risky' => true,
        ]);
    }

    function it_adds_a_php_version_support()
    {
        $this->php(5.6)->getRules()->shouldReturn([
            '@PHP56Migration' => true,
            'array_syntax'    => ['syntax' => 'short'],
            'list_syntax'     => ['syntax' => 'long'],
        ]);

        $this->php(5.6, true)->getRules()->shouldReturn([
            '@PHP56Migration'       => true,
            '@PHP56Migration:risky' => true,
            'array_syntax'          => ['syntax' => 'short'],
            'list_syntax'           => ['syntax' => 'long'],
        ]);

        $this->php(7.0)->getRules()->shouldReturn([
            '@PHP56Migration' => true,
            '@PHP70Migration' => true,
            'array_syntax'    => ['syntax' => 'short'],
            'list_syntax'     => ['syntax' => 'long'],
        ]);

        $this->php(7.0, true)->getRules()->shouldReturn([
            '@PHP56Migration'       => true,
            '@PHP56Migration:risky' => true,
            '@PHP70Migration'       => true,
            '@PHP70Migration:risky' => true,
            'array_syntax'          => ['syntax' => 'short'],
            'list_syntax'           => ['syntax' => 'long'],
        ]);

        $this->php(7.1)->getRules()->shouldReturn([
            '@PHP56Migration' => true,
            '@PHP70Migration' => true,
            '@PHP71Migration' => true,
            'array_syntax'    => ['syntax' => 'short'],
            'list_syntax'     => ['syntax' => 'short'],
        ]);

        $this->php(7.1, true)->getRules()->shouldReturn([
            '@PHP56Migration'       => true,
            '@PHP56Migration:risky' => true,
            '@PHP70Migration'       => true,
            '@PHP70Migration:risky' => true,
            '@PHP71Migration'       => true,
            '@PHP71Migration:risky' => true,
            'array_syntax'          => ['syntax' => 'short'],
            'list_syntax'           => ['syntax' => 'short'],
        ]);

        $this->php(7.2)->getRules()->shouldReturn([
            '@PHP56Migration' => true,
            '@PHP70Migration' => true,
            '@PHP71Migration' => true,
            'array_syntax'    => ['syntax' => 'short'],
            'list_syntax'     => ['syntax' => 'short'],
        ]);

        $this->php(7.2, true)->getRules()->shouldReturn([
            '@PHP56Migration'       => true,
            '@PHP56Migration:risky' => true,
            '@PHP70Migration'       => true,
            '@PHP70Migration:risky' => true,
            '@PHP71Migration'       => true,
            '@PHP71Migration:risky' => true,
            'array_syntax'          => ['syntax' => 'short'],
            'list_syntax'           => ['syntax' => 'short'],
        ]);
    }

    function it_can_also_parse_versions_as_string()
    {
        $this->php('5.6.2')->getRules()->shouldReturn([
            '@PHP56Migration' => true,
            'array_syntax'    => ['syntax' => 'short'],
            'list_syntax'     => ['syntax' => 'long'],
        ]);
    }

    function it_adds_a_phpunit_version_support()
    {
        $this->phpUnit(2.0, false)->getRules()->shouldReturn([]);

        $this->phpUnit(2.0, true)->getRules()->shouldReturn([]);

        $this->phpUnit(3.0, false)->getRules()->shouldReturn([]);

        $this->phpUnit(3.0, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
        ]);

        $this->phpUnit(3.2, false)->getRules()->shouldReturn([]);

        $this->phpUnit(3.2, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
        ]);

        $this->phpUnit(3.5, false)->getRules()->shouldReturn([]);

        $this->phpUnit(3.5, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
        ]);

        $this->phpUnit(4.3, false)->getRules()->shouldReturn([]);

        $this->phpUnit(4.3, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
            '@PHPUnit43Migration:risky' => true,
        ]);

        $this->phpUnit(4.8, false)->getRules()->shouldReturn([]);

        $this->phpUnit(4.8, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
            '@PHPUnit43Migration:risky' => true,
            '@PHPUnit48Migration:risky' => true,
        ]);

        $this->phpUnit(5.0, false)->getRules()->shouldReturn([]);

        $this->phpUnit(5.0, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
            '@PHPUnit43Migration:risky' => true,
            '@PHPUnit48Migration:risky' => true,
            '@PHPUnit50Migration:risky' => true,
        ]);

        $this->phpUnit(5.2, false)->getRules()->shouldReturn([]);

        $this->phpUnit(5.2, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
            '@PHPUnit43Migration:risky' => true,
            '@PHPUnit48Migration:risky' => true,
            '@PHPUnit50Migration:risky' => true,
            '@PHPUnit52Migration:risky' => true,
        ]);

        $this->phpUnit(5.4, false)->getRules()->shouldReturn([]);

        $this->phpUnit(5.4, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
            '@PHPUnit43Migration:risky' => true,
            '@PHPUnit48Migration:risky' => true,
            '@PHPUnit50Migration:risky' => true,
            '@PHPUnit52Migration:risky' => true,
            '@PHPUnit54Migration:risky' => true,
        ]);

        $this->phpUnit(5.5, false)->getRules()->shouldReturn([]);

        $this->phpUnit(5.5, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
            '@PHPUnit43Migration:risky' => true,
            '@PHPUnit48Migration:risky' => true,
            '@PHPUnit50Migration:risky' => true,
            '@PHPUnit52Migration:risky' => true,
            '@PHPUnit54Migration:risky' => true,
            '@PHPUnit55Migration:risky' => true,
        ]);

        $this->phpUnit(5.6, false)->getRules()->shouldReturn([]);

        $this->phpUnit(5.6, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
            '@PHPUnit43Migration:risky' => true,
            '@PHPUnit48Migration:risky' => true,
            '@PHPUnit50Migration:risky' => true,
            '@PHPUnit52Migration:risky' => true,
            '@PHPUnit54Migration:risky' => true,
            '@PHPUnit55Migration:risky' => true,
            '@PHPUnit56Migration:risky' => true,
        ]);

        $this->phpUnit(5.7, false)->getRules()->shouldReturn([]);

        $this->phpUnit(5.7, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
            '@PHPUnit43Migration:risky' => true,
            '@PHPUnit48Migration:risky' => true,
            '@PHPUnit50Migration:risky' => true,
            '@PHPUnit52Migration:risky' => true,
            '@PHPUnit54Migration:risky' => true,
            '@PHPUnit55Migration:risky' => true,
            '@PHPUnit56Migration:risky' => true,
            '@PHPUnit57Migration:risky' => true,
        ]);

        $this->phpUnit(6.0, false)->getRules()->shouldReturn([]);

        $this->phpUnit(6.0, true)->getRules()->shouldReturn([
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit32Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
            '@PHPUnit43Migration:risky' => true,
            '@PHPUnit48Migration:risky' => true,
            '@PHPUnit50Migration:risky' => true,
            '@PHPUnit52Migration:risky' => true,
            '@PHPUnit54Migration:risky' => true,
            '@PHPUnit55Migration:risky' => true,
            '@PHPUnit56Migration:risky' => true,
            '@PHPUnit57Migration:risky' => true,
            '@PHPUnit60Migration:risky' => true,
        ]);
    }

    function it_adds_my_own_fixer_set()
    {
        $rules = [];

        foreach (new Fixers() as $fixer) {
            if ($fixer->isDeprecated()) {
                continue;
            }

            $rules[$fixer->getName()] = true;
        }

        ksort($rules);

        $this->pedrotroller()->getRules()->shouldReturn($rules);
    }

    function it_enables_a_rule()
    {
        $this
            ->enable('no_useless_else')
            ->enable('ordered_imports')
            ->enable('phpdoc_add_missing_param_annotation', ['only_untyped' => true])
            ->getRules()
            ->shouldReturn([
                'no_useless_else'                     => true,
                'ordered_imports'                     => true,
                'phpdoc_add_missing_param_annotation' => ['only_untyped' => true],
            ])
        ;
    }

    function it_disables_a_rule()
    {
        $this
            ->enable('no_useless_else')
            ->enable('ordered_imports')
            ->enable('phpdoc_add_missing_param_annotation', ['only_untyped' => true])
            ->disable('phpdoc_add_missing_param_annotation')
            ->getRules()
            ->shouldReturn([
                'no_useless_else'                     => true,
                'ordered_imports'                     => true,
                'phpdoc_add_missing_param_annotation' => false,
            ])
        ;
    }
}
