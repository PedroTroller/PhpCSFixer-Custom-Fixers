<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use Exception;
use IteratorAggregate;
use PhpCsFixer\RuleSet\RuleSets;
use Traversable;

/**
 * @IteratorAggregate<string, bool|array<mixed>>
 */
final class RuleSetFactory implements IteratorAggregate
{
    /**
     * @var array<string, array<mixed>|bool>
     */
    private $rules;

    /**
     * @var array<string>
     */
    private array $cache;

    /**
     * @param array<string, array<mixed>|bool> $rules
     * @param array<string>                    $cache
     */
    private function __construct(array $rules, array $cache)
    {
        $this->rules = $rules;
        $this->cache = $cache;
    }

    /**
     * @return array<string, array<mixed>|bool>
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function getIterator(): Traversable
    {
        yield from $this->rules;
    }

    public static function create(array $rules = []): self
    {
        return new self(
            $rules,
            (new RuleSets())->getSetDefinitionNames(),
        );
    }

    public function per(int|float $version = null, bool $risky = false): self
    {
        $candidates = null !== $version
            ? ['@PER-CS'.number_format($version, 1, '.', '')]
            : ['@PER'];

        if (true === $risky) {
            $candidates = [
                $candidates[0].':risky',
                ...$candidates,
            ];
        }

        foreach ($candidates as $candidate) {
            if (false === \in_array($candidate, $this->cache, true)) {
                continue;
            }

            return self::create(array_merge(
                $this->rules,
                [$candidate => true],
            ));
        }

        throw new Exception('RuleSet not found: '.implode(', ', $candidates));
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

        $config['array_syntax'] = ['syntax' => 'long'];
        $config['list_syntax']  = ['syntax' => 'long'];

        if ($version >= 7.1) {
            $config['list_syntax'] = ['syntax' => 'short'];
        }

        if ($version >= 5.4) {
            $config['array_syntax'] = ['syntax' => 'short'];
        }

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
            if ($fixer->isDeprecated()) {
                continue;
            }

            if (false === $risky && $fixer->isRisky()) {
                continue;
            }

            $rules[$fixer->getName()] = true;
        }

        return self::create(array_merge(
            $this->rules,
            $rules
        ));
    }

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
        $rules = array_combine($this->cache, $this->cache);
        $rules = array_map(
            static function ($name) {
                preg_match('/^@([A-Za-z]+)(\d+)Migration(:risky|)$/', $name, $matches);

                return $matches;
            },
            $rules
        );

        $rules = array_filter($rules);

        $rules = array_filter($rules, static function ($versionAndRisky) use ($package) {
            [$rule, $rulePackage, $ruleVersion, $ruleRisky] = $versionAndRisky;

            return strtoupper($package) === strtoupper($rulePackage);
        });

        $rules = array_filter($rules, static function ($versionAndRisky) use ($version) {
            [$rule, $rulePackage, $ruleVersion, $ruleRisky] = $versionAndRisky;

            return ((float) $ruleVersion / 10) <= $version;
        });

        $rules = array_filter($rules, static function ($versionAndRisky) use ($risky) {
            [$rule, $rulePackage, $ruleVersion, $ruleRisky] = $versionAndRisky;

            if ($risky) {
                return true;
            }

            return empty($ruleRisky);
        });

        return self::create(array_merge(
            $this->rules,
            array_map(static fn () => true, $rules)
        ));
    }
}
