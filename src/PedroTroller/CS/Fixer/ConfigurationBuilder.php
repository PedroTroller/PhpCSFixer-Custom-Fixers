<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

final class ConfigurationBuilder
{
    private function __construct()
    {
    }

    /**
     * @param float $version
     *
     * @return array
     */
    public static function php($version)
    {
        $config = [];

        switch (true) {
            case $version >= 7.1:
                $config = array_merge(['list_syntax' => ['syntax' => 'short']], $config);
                $config = array_merge(['@PHP71Migration' => true], $config);
                // no break
            case $version >= 7.0:
                $config = array_merge(['@PHP70Migration' => true], $config);
                // no break
            case $version >= 5.6:
                $config = array_merge(['@PHP56Migration' => true], $config);
                // no break
            case $version >= 5.4:
                $config = array_merge(['array_syntax' => ['syntax' => 'short']], $config);
        }

        $config = array_merge(['list_syntax' => ['syntax' => 'long']], $config);
        $config = array_merge(['array_syntax' => ['syntax' => 'long']], $config);

        return $config;
    }

    /**
     * @return array
     */
    public static function buildBasicConfiguration()
    {
        $config = [];

        foreach (new Fixers() as $fixer) {
            $config[$fixer->getName()] = true;
        }

        return $config;
    }

    /**
     * @return array
     */
    public static function buildCustomConfiguration()
    {
        return array_merge(
            self::buildBasicConfiguration(),
            [
                '@Symfony'                                  => true,
                'align_multiline_comment'                   => true,
                'array_indentation'                         => true,
                'binary_operator_spaces'                    => ['operators' => ['=' => 'align_single_space_minimal', '=>' => 'align_single_space_minimal']],
                'blank_line_before_statement'               => true,
                'combine_consecutive_issets'                => true,
                'combine_consecutive_unsets'                => true,
                'compact_nullable_typehint'                 => true,
                'escape_implicit_backslashes'               => true,
                'explicit_indirect_variable'                => true,
                'explicit_string_variable'                  => true,
                'general_phpdoc_annotation_remove'          => true,
                'heredoc_to_nowdoc'                         => true,
                'linebreak_after_opening_tag'               => true,
                'mb_str_functions'                          => true,
                'method_chaining_indentation'               => true,
                'multiline_comment_opening_closing'         => true,
                'multiline_whitespace_before_semicolons'    => ['strategy' => 'new_line_for_chained_calls'],
                'no_alternative_syntax'                     => true,
                'no_multiline_whitespace_before_semicolons' => true,
                'no_null_property_initialization'           => true,
                'no_superfluous_elseif'                     => true,
                'no_useless_else'                           => true,
                'no_useless_return'                         => true,
                'ordered_class_elements'                    => true,
                'ordered_imports'                           => true,
                'phpdoc_add_missing_param_annotation'       => ['only_untyped' => true],
                'phpdoc_order'                              => true,
                'phpdoc_types_order'                        => true,
                'semicolon_after_instruction'               => true,
                'simplified_null_return'                    => true,
                'single_line_comment_style'                 => true,
                'standardize_increment'                     => true,
            ]
        );
    }
}
