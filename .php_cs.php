<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

$header = <<<EOF
This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.

(c) A51 doo <info@activecollab.com>
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return (new Symfony\CS\Config\Config('psr2'))->fixers([
    'header_comment',
    'array_element_no_space_before_comma',
    'array_element_white_space_after_comma',
    'double_arrow_multiline_whitespaces',
    'hash_to_slash_comment',
    'include',
    'join_function',
    'multiline_array_trailing_comma',
    'namespace_no_leading_whitespace',
    'no_blank_lines_after_class_opening ',
    'no_empty_lines_after_phpdocs',
    'phpdoc_scalar',
    'phpdoc_short_description',
    'self_accessor',
    'single_array_no_trailing_comma',
    'single_blank_line_before_namespace',
    'spaces_after_semicolon',
    'spaces_before_semicolon',
    'spaces_cast',
    'standardize_not_equal',
    'ternary_spaces',
    'trim_array_spaces',
    'unused_use ',
    'whitespacy_lines',
    'ordered_use',
    'short_array_syntax',
    'phpdoc_params',
    '-phpdoc_separation',
    '-phpdoc_no_package',
    '-print_to_echo',
    '-concat_without_spaces',
    '-empty_return',
])->finder((new Symfony\CS\Finder\DefaultFinder())->in([__DIR__ . '/src']));
