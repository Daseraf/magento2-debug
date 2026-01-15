<?php
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$header = <<<EOF
Designed by Stanislav Matiavin
EOF;
$finder = (new Finder())
    ->ignoreDotFiles(false)
    ->ignoreVCSIgnored(true)
    ->exclude(['dev-tools/phpstan', 'tests/Fixtures'])
    ->in('.')
    ->name('*.phtml')
    ->notName(['autoload.php', 'bootstrap.php'])
    ->exclude('i18n')
    ->exclude('design')
    ->exclude('etc')
    ->exclude('vendor');

$rules = ['@PSR2' => true,
    'array_syntax' => ['syntax' => 'short'],
    'concat_space' => ['spacing' => 'one'],
    'include' => true,
    'new_with_parentheses' => true,
    'no_empty_statement' => true,
    'no_leading_import_slash' => true,
    'no_leading_namespace_whitespace' => true,
    'no_multiline_whitespace_around_double_arrow' => true,
    'multiline_whitespace_before_semicolons' => true,
    'no_unused_imports' => true,
    'ordered_imports' => true,
    'ternary_operator_spaces' => true,
    'phpdoc_order' => true,
    'phpdoc_types' => true,
    'phpdoc_add_missing_param_annotation' => true,
    'single_quote' => true,
    'standardize_not_equals' => true,
    'ternary_to_null_coalescing' => true,
    'lowercase_cast' => true,
    'no_empty_comment' => true,
    'no_empty_phpdoc' => true,
    'return_type_declaration' => true,
    'no_useless_return' => true,
    'align_multiline_comment' => true,
    'array_indentation' => true,
    'binary_operator_spaces' => true,
    'blank_line_after_opening_tag' => true,
    'blank_line_before_statement' => ['statements' => ["return", "throw", "try"]],
    'cast_spaces' => true,
    'class_attributes_separation' => true,
    'explicit_indirect_variable' => true,
    'explicit_string_variable' => true,
    'type_declaration_spaces' => true,
    'lowercase_static_reference' => true,
    'method_chaining_indentation' => true,
    'multiline_comment_opening_closing' => true,
    'native_function_casing' => true,
    'no_blank_lines_after_class_opening' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_extra_blank_lines' => [
        'tokens' => ["break",
            "continue",
            "curly_brace_block",
            "extra",
            "parenthesis_brace_block",
            "return",
            "square_brace_block",
            "throw",
            "use"]
    ],
    'no_short_bool_cast' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_spaces_around_offset' => true,
    'no_superfluous_elseif' => true,
    'no_trailing_comma_in_singleline' => true,
    'no_useless_else' => true,
    'no_whitespace_in_blank_line' => true,
    'object_operator_without_whitespace' => true,
    'ordered_class_elements' => [
        'order' => ["use_trait",
            "constant_public",
            "constant_protected",
            "constant_private",
            "property_public",
            "property_protected",
            "property_private",
            "construct",
            "destruct",
            "magic",
            "phpunit",
            "method_public",
            "method_protected",
            "method_private"]
    ],
    'phpdoc_align' => ['align' => 'left'],
    'phpdoc_indent' => true,
    'phpdoc_return_self_reference' => true,
    'phpdoc_scalar' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_trim' => true,
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last'
    ],
    'return_assignment' => true,
    'trailing_comma_in_multiline' => ['elements' => ['arrays']],
    'trim_array_spaces' => true,
    'unary_operator_spaces' => true,
    'declare_strict_types' => true,
    'void_return' => true,
    'whitespace_after_comma_in_array' => true];

if (null !== $header) {
    $rules['header_comment'] = [
        'comment_type' => 'PHPDoc',
        'header' => $header,
        'location' => 'after_open',
        'separate' => 'bottom',
    ];
}

return (new Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules($rules);
