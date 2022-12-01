<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use IteratorAggregate;
use PhpCsFixer\RuleSet\RuleSets;
use Traversable;

/**
 * @implements IteratorAggregate<string, bool|array<string, mixed>>
 */
final class RuleSetFactory implements IteratorAggregate
{
    /**
     * @var array<string, array<string, mixed>|bool>
     */
    private array $rules;

    /**
     * @param array<string, array<string, mixed>|bool> $rules
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * @return array<string, array<string, mixed>|bool>
     */
    public function getRules(): array
    {
        return [...$this];
    }

    public function getIterator(): Traversable
    {
        $rules = $this->rules;

        ksort($rules);

        yield from $rules;
    }

    /**
     * @param array<string, array<string, mixed>|bool> $rules
     */
    public static function create(array $rules = []): self
    {
        return new self($rules);
    }

    public function psr0(): self
    {
        return self::create(array_merge(
            $this->rules,
            ['@psr0' => true]
        ));
    }

    public function psr1(): self
    {
        return self::create(array_merge(
            $this->rules,
            ['@psr1' => true]
        ));
    }

    public function psr2(): self
    {
        return self::create(array_merge(
            $this->rules,
            ['@psr2' => true]
        ));
    }

    public function psr4(): self
    {
        return self::create(array_merge(
            $this->rules,
            ['@psr4' => true]
        ));
    }

    public function symfony(bool $risky = false): self
    {
        $rules = ['@Symfony' => true];

        if ($risky) {
            $rules['@Symfony:risky'] = true;
        }

        return self::create(array_merge(
            $this->rules,
            $rules
        ));
    }

    public function phpCsFixer(bool $risky = false): self
    {
        $rules = ['@PhpCsFixer' => true];

        if ($risky) {
            $rules['@PhpCsFixer:risky'] = true;
        }

        return self::create(array_merge(
            $this->rules,
            $rules
        ));
    }

    public function doctrineAnnotation(): self
    {
        return self::create(array_merge(
            $this->rules,
            ['@DoctrineAnnotation' => true]
        ));
    }

    public function php(float $version, bool $risky = false): self
    {
        $config = $this->migration('php', $version, $risky)->getRules();

        switch (true) {
            case $version >= 7.1:
                $config = array_merge(['list_syntax' => ['syntax' => 'short']], $config);
                // no break
            case $version >= 5.4:
                $config = array_merge(['array_syntax' => ['syntax' => 'short']], $config);
        }

        $config = array_merge(['list_syntax' => ['syntax' => 'long']], $config);
        $config = array_merge(['array_syntax' => ['syntax' => 'long']], $config);

        return self::create(array_merge(
            $this->rules,
            $config
        ));
    }

    public function phpUnit(float $version, bool $risky = false): self
    {
        return $this->migration('phpunit', $version, $risky);
    }

    public function pedrotroller(bool $risky = false): self
    {
        $rules = [];

        foreach (new Fixers() as $fixer) {
            if ($fixer instanceof AbstractFixer && $fixer->isDeprecated()) {
                continue;
            }

            $rules[$fixer->getName()] = true;
        }

        ksort($rules);

        return self::create(array_merge(
            $this->rules,
            $rules
        ));
    }

    /**
     * @param array<array<string, mixed>> $config
     */
    public function enable(string $name, array $config = null): self
    {
        return self::create(array_merge(
            $this->rules,
            [$name => \is_array($config) ? $config : true]
        ));
    }

    public function disable(string $name): self
    {
        return self::create(array_merge(
            $this->rules,
            [$name => false]
        ));
    }

    private function migration(string $package, float $version, bool $risky): self
    {
        $rules = (new RuleSets())->getSetDefinitionNames();
        $rules = array_combine($rules, $rules);

        $rules = array_map(function ($name) {
            preg_match('/^@([A-Za-z]+)(\d+)Migration(:risky|)$/', $name, $matches);

            return $matches;
        }, $rules);

        $rules = array_filter($rules);

        $rules = array_filter($rules, function ($versionAndRisky) use ($package) {
            [$rule, $rulePackage, $ruleVersion, $ruleRisky] = $versionAndRisky;

            return strtoupper($package) === strtoupper($rulePackage);
        });

        $rules = array_filter($rules, function ($versionAndRisky) use ($version) {
            [$rule, $rulePackage, $ruleVersion, $ruleRisky] = $versionAndRisky;

            return ((float) $ruleVersion / 10) <= $version;
        });

        $rules = array_filter($rules, function ($versionAndRisky) use ($risky) {
            [$rule, $rulePackage, $ruleVersion, $ruleRisky] = $versionAndRisky;

            if ($risky) {
                return true;
            }

            return empty($ruleRisky);
        });

        return self::create(array_merge(
            $this->rules,
            array_map(fn () => true, $rules)
        ));
    }
}
