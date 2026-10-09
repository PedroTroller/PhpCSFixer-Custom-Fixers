<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use Exception;
use IteratorAggregate;
use PhpCsFixer\RuleSet\RuleSets;
use Traversable;

/**
 * @implements IteratorAggregate<string, array<mixed>|bool>
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

    /**
     * @return Traversable<string, array<mixed>|bool>
     */
    public function getIterator(): Traversable
    {
        yield from $this->rules;
    }

    /**
     * @param array<string, array<mixed>|bool> $rules
     */
    public static function create(array $rules = []): self
    {
        return new self(
            $rules,
            (new RuleSets())->getSetDefinitionNames(),
        );
    }

    public function per(float|int|null $version = null, bool $risky = false): self
    {
        $candidates = null !== $version
            ? ['@PER-CS'.number_format($version, 1, 'x', '')]
            : ['@PER-CS'];

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

            return self::create(
                array_merge(
                    $this->rules,
                    [$candidate => true],
                )
            );
        }

        throw new Exception('RuleSet not found: '.implode(', ', $candidates));
    }

    public function psr1(): self
    {
        return self::create(
            array_merge(
                $this->rules,
                ['@PSR1' => true]
            )
        );
    }

    public function psr2(): self
    {
        return self::create(
            array_merge(
                $this->rules,
                ['@PSR2' => true]
            )
        );
    }

    public function psr12(bool $risky = false): self
    {
        $rules = ['@PSR12' => true];

        if ($risky) {
            $rules['@PSR12:risky'] = true;
        }

        return self::create(
            array_merge(
                $this->rules,
                $rules
            )
        );
    }

    public function auto(bool $risky = false): self
    {
        $rules = ['@auto' => true];

        if ($risky) {
            $rules['@auto:risky'] = true;
        }

        return self::create(
            array_merge(
                $this->rules,
                $rules
            )
        );
    }

    public function symfony(bool $risky = false): self
    {
        $rules = ['@Symfony' => true];

        if ($risky) {
            $rules['@Symfony:risky'] = true;
        }

        return self::create(
            array_merge(
                $this->rules,
                $rules
            )
        );
    }

    public function phpCsFixer(bool $risky = false): self
    {
        $rules = ['@PhpCsFixer' => true];

        if ($risky) {
            $rules['@PhpCsFixer:risky'] = true;
        }

        return self::create(
            array_merge(
                $this->rules,
                $rules
            )
        );
    }

    public function doctrineAnnotation(): self
    {
        return self::create(
            array_merge(
                $this->rules,
                ['@DoctrineAnnotation' => true]
            )
        );
    }

    public function php(?float $version = null, bool $risky = false): self
    {
        if (null === $version) {
            $config = ['@autoPHPMigration' => true];

            if ($risky) {
                $config['@autoPHPMigration:risky'] = true;
            }

            $config['array_syntax'] = ['syntax' => 'short'];
            $config['list_syntax']  = ['syntax' => 'short'];

            return self::create(
                array_merge(
                    $this->rules,
                    $config
                )
            );
        }

        $config = $this->migration('php', $version, $risky)->getRules();

        $config['array_syntax'] = ['syntax' => 'long'];
        $config['list_syntax']  = ['syntax' => 'long'];

        if ($version >= 7.1) {
            $config['list_syntax'] = ['syntax' => 'short'];
        }

        if ($version >= 5.4) {
            $config['array_syntax'] = ['syntax' => 'short'];
        }

        return self::create(
            array_merge(
                $this->rules,
                $config
            )
        );
    }

    public function phpUnit(?float $version = null, bool $risky = false): self
    {
        if (null === $version) {
            // php-cs-fixer only ships a risky flavour of @autoPHPUnitMigration.
            return $risky ? $this->enable('@autoPHPUnitMigration:risky') : $this;
        }

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

        return self::create(
            array_merge(
                $this->rules,
                $rules
            )
        );
    }

    /**
     * @param null|array<mixed> $config
     */
    public function enable(string $name, ?array $config = null): self
    {
        return self::create(
            array_merge(
                $this->rules,
                [$name => \is_array($config) ? $config : true]
            )
        );
    }

    public function disable(string $name): self
    {
        return self::create(
            array_merge(
                $this->rules,
                [$name => false]
            )
        );
    }

    private function migration(string $package, float $version, bool $risky): self
    {
        $rules = array_combine($this->cache, $this->cache);
        $rules = array_map(
            static function ($name) {
                preg_match('/^@([A-Za-z]+)(\d+)x(\d+)Migration(:risky|)$/', $name, $matches);

                return $matches;
            },
            $rules
        );

        $rules = array_filter($rules);

        $rules = array_filter(
            $rules,
            static function ($versionAndRisky) use ($package) {
                [$rule, $rulePackage, $ruleVersionMajor, $ruleVersionMinor, $ruleRisky] = $versionAndRisky;

                return strtoupper($package) === strtoupper($rulePackage);
            }
        );

        $rules = array_filter(
            $rules,
            static function ($versionAndRisky) use ($version) {
                [$rule, $rulePackage, $ruleVersionMajor, $ruleVersionMinor, $ruleRisky] = $versionAndRisky;

                return ((float) ($ruleVersionMajor.'.'.$ruleVersionMinor)) <= $version;
            }
        );

        $rules = array_filter(
            $rules,
            static function ($versionAndRisky) use ($risky) {
                [$rule, $rulePackage, $ruleVersionMajor, $ruleVersionMinor, $ruleRisky] = $versionAndRisky;

                if ($risky) {
                    return true;
                }

                return empty($ruleRisky);
            }
        );

        return self::create(
            array_merge(
                $this->rules,
                array_map(static fn () => true, $rules)
            )
        );
    }
}
