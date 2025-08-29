<?php

namespace Helmich\TypoScriptParser\Tests\Unit\Tokenizer;

use Helmich\TypoScriptParser\Tokenizer\Token;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use Helmich\TypoScriptParser\Tokenizer\TokenizerException;
use Helmich\TypoScriptParser\Tokenizer\TokenizerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use VirtualFileSystem\FileSystem;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

class TokenizerTest extends TestCase
{
    private TokenizerInterface $tokenizer;

    public function setUp(): void
    {
        $this->tokenizer = new Tokenizer();
    }

    public static function dataValidForTokenizer(): array
    {
        return [
            "assignment with line breaks"                          => ["foo = bar\n\n", [
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
                new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1, 5),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 6),
                new Token(Token::TYPE_RIGHTVALUE, "bar", 1, 7),
                new Token(Token::TYPE_EMPTY_LINE, "\n", 2, 1),
                new Token(Token::TYPE_EMPTY_LINE, "\n", 3, 1),
            ]],
            "assignment"                          => ["foo = bar", [
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
                new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1, 5),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 6),
                new Token(Token::TYPE_RIGHTVALUE, "bar", 1, 7),
            ]],

            // assert that trailing whitespaces are simply ignored
            "assignment with trailing whitespace" => ["foo = bar ", [
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
                new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1, 5),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 6),
                new Token(Token::TYPE_RIGHTVALUE, "bar", 1, 7),
            ]],

            "nested assignment" => ["foo.bar = baz", [
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo.bar", 1, 1),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 8),
                new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1, 9),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 10),
                new Token(Token::TYPE_RIGHTVALUE, "baz", 1, 11),
            ]],

            "double-nested assignment" => ["foo.bar.baz = baz", [
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo.bar.baz", 1, 1),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 12),
                new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1, 13),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 14),
                new Token(Token::TYPE_RIGHTVALUE, "baz", 1, 15),
            ]],

            "reference assignment" => ["foo =< bar", [
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
                new Token(Token::TYPE_OPERATOR_REFERENCE, "=<", 1, 5),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 7),
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "bar", 1, 8),
            ]],

            "deletion with comment" => ["foo > # Something", [
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
                new Token(Token::TYPE_OPERATOR_DELETE, ">", 1, 5),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 6),
                new Token(Token::TYPE_COMMENT_ONELINE, "# Something", 1, 7),
            ]],

            // https://github.com/martin-helmich/typo3-typoscript-parser/issues/58
            // https://github.com/martin-helmich/typo3-typoscript-parser/issues/50
            "modification with brace" => ["foo := addToList(bar())", [
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
                new Token(Token::TYPE_OPERATOR_MODIFY, ":=", 1, 5),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 7),
                new Token(Token::TYPE_OBJECT_MODIFIER, "addToList(bar())", 1, 8, [
                    0           => 'addToList(bar())',
                    'name'      => 'addToList',
                    1           => 'addToList',
                    'arguments' => 'bar()',
                    2           => 'bar()',
                ]),
            ]],

            "include file, old syntax"                                 => ["<INCLUDE_TYPOSCRIPT: source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">", [
                new Token(Token::TYPE_INCLUDE, "<INCLUDE_TYPOSCRIPT: source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">", 1, 1, [
                    0          => "<INCLUDE_TYPOSCRIPT: source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">",
                    'type'     => 'FILE',
                    1          => 'FILE',
                    'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    2          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    'optional' => '',
                    3          => '',
                ]),
            ]],

            // https://github.com/martin-helmich/typo3-typoscript-parser/issues/30
            "include file, old syntax, no space"                       => ["<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">", [
                new Token(Token::TYPE_INCLUDE, "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">", 1, 1, [
                    0          => "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">",
                    'type'     => 'FILE',
                    1          => 'FILE',
                    'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    2          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    'optional' => '',
                    3          => '',
                ]),
            ]],

            // https://github.com/martin-helmich/typo3-typoscript-lint/issues/63
            "include file, old syntax, with extensions"                => ["<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\">", [
                new Token(Token::TYPE_INCLUDE, "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\">", 1, 1, [
                    0          => "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\">",
                    'type'     => 'FILE',
                    1          => 'FILE',
                    'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    2          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    'optional' => 'extensions="typoscript"',
                    3          => 'extensions="typoscript"',
                ]),
            ]],

            // https://github.com/martin-helmich/typo3-typoscript-lint/issues/63
            "include file, old syntax, with condition"                 => ["<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">", [
                new Token(Token::TYPE_INCLUDE, "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">", 1, 1, [
                    0          => "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">",
                    'type'     => 'FILE',
                    1          => 'FILE',
                    'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    2          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    'optional' => 'condition="YourVendor\YourPackage\YourCondition"',
                    3          => 'condition="YourVendor\YourPackage\YourCondition"',
                ]),
            ]],

            // https://github.com/martin-helmich/typo3-typoscript-lint/issues/63
            "include file, old syntax, with extensions and conditions" => ["<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">", [
                new Token(Token::TYPE_INCLUDE, "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">", 1, 1, [
                    0          => "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">",
                    'type'     => 'FILE',
                    1          => 'FILE',
                    'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    2          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    'optional' => 'extensions="typoscript" condition="YourVendor\YourPackage\YourCondition"',
                    3          => 'extensions="typoscript" condition="YourVendor\YourPackage\YourCondition"',
                ]),
            ]],

            "include file, new syntax, single quotes" => ["@import 'EXT:foo/Configuration/TypoScript/setup.typoscript'", [
                new Token(Token::TYPE_INCLUDE_NEW, "@import 'EXT:foo/Configuration/TypoScript/setup.typoscript'", 1, 1, [
                    0          => "@import 'EXT:foo/Configuration/TypoScript/setup.typoscript'",
                    'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    1          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                ]),
            ]],

            // https://github.com/martin-helmich/typo3-typoscript-lint/issues/89
            "include file, new syntax, double quotes" => ["@import \"EXT:foo/Configuration/TypoScript/setup.typoscript\"", [
                new Token(Token::TYPE_INCLUDE_NEW, "@import \"EXT:foo/Configuration/TypoScript/setup.typoscript\"", 1, 1, [
                    0          => "@import \"EXT:foo/Configuration/TypoScript/setup.typoscript\"",
                    'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                    1          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                ]),
            ]],

            "condition, old syntax" => ["[globalVar = GP:L = 1]", [
                new Token(Token::TYPE_CONDITION, "[globalVar = GP:L = 1]", 1, 1, [
                    0      => "[globalVar = GP:L = 1]",
                    1      => "[globalVar = GP:L = 1]",
                    2      => 'globalVar = GP:L = 1',
                    'expr' => 'globalVar = GP:L = 1',
                    3      => '',
                ]),
            ]],

            "condition, Symfony expression #1" => ['[siteLanguage("languageId") == "1"]', [
                new Token(Token::TYPE_CONDITION, '[siteLanguage("languageId") == "1"]', 1, 1, [
                    0      => '[siteLanguage("languageId") == "1"]',
                    1      => '[siteLanguage("languageId") == "1"]',
                    2      => 'siteLanguage("languageId") == "1"',
                    'expr' => 'siteLanguage("languageId") == "1"',
                    3      => '',
                ]),
            ]],

            "condition, Symfony expression #2" => ['[1 in tree.rootLineIds]', [
                new Token(Token::TYPE_CONDITION, '[1 in tree.rootLineIds]', 1, 1, [
                    0      => '[1 in tree.rootLineIds]',
                    1      => '[1 in tree.rootLineIds]',
                    2      => '1 in tree.rootLineIds',
                    'expr' => '1 in tree.rootLineIds',
                    3      => '',
                ]),
            ]],

            "condition termination, using global" => ['[global]', [
                new Token(Token::TYPE_CONDITION_END, '[global]', 1, 1, [
                    0 => '[global]',
                    1 => 'global',
                    'keyword' => 'global',
                ]),
            ]],

            "condition termination, using end" => ['[end]', [
                new Token(Token::TYPE_CONDITION_END, '[end]', 1, 1, [
                    0 => '[end]',
                    1 => 'end',
                    'keyword' => 'end',
                ]),
            ]],

            "deep nested assignment" => [
                <<<CODE
temp {
    code {
        one = 1
    }
}
CODE
                , [
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "temp", 1, 1),
                new Token(Token::TYPE_WHITESPACE, " ", 1, 5),
                new Token(Token::TYPE_BRACE_OPEN, "{", 1, 6),
                new Token(Token::TYPE_WHITESPACE, "\n", 1, 7),

                new Token(Token::TYPE_WHITESPACE, "    ", 2, 1),
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "code", 2, 5),
                new Token(Token::TYPE_WHITESPACE, " ", 2, 9),
                new Token(Token::TYPE_BRACE_OPEN, "{", 2, 10),
                new Token(Token::TYPE_WHITESPACE, "\n", 2, 11),

                new Token(Token::TYPE_WHITESPACE, "        ", 3, 1),
                new Token(Token::TYPE_OBJECT_IDENTIFIER, "one", 3, 9),
                new Token(Token::TYPE_WHITESPACE, " ", 3, 12),
                new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 3, 13),
                new Token(Token::TYPE_WHITESPACE, " ", 3, 14),
                new Token(Token::TYPE_RIGHTVALUE, "1", 3, 15),
                new Token(Token::TYPE_WHITESPACE, "\n", 3, 16),

                new Token(Token::TYPE_WHITESPACE, "    ", 4, 1),
                new Token(Token::TYPE_BRACE_CLOSE, "}", 4, 5, ['}']),
                new Token(Token::TYPE_WHITESPACE, "\n", 4, 6),

                new Token(Token::TYPE_BRACE_CLOSE, "}", 5, 1, ['}']),
            ]],

            "multiline comment" => [
                <<<CODE
/*
    temp {
        one = 1
    }
*/
CODE
                , [
                    new Token(Token::TYPE_COMMENT_MULTILINE, <<<CODE
/*
    temp {
        one = 1
    }
*/
CODE, 1, 1),

                ]],
        ];
    }

    public static function dataInvalidForTokenizer(): array
    {
        return [
            "unterminated multiline assignment" => ["a (\nasdf"],
            "unterminated block comment" => ["/*\nhello world"],
            "invalid operator !=" => ["foo != bar"],
            "invalid operator *=" => ["foo *= bar"],
        ];
    }

    #[DataProvider('dataValidForTokenizer')]
    public function testInputTextIsCorrectlyTokenized($inputText, $expectedTokenStream)
    {
        $tokenStream = $this->tokenizer->tokenizeString($inputText);
        assertThat($tokenStream, equalTo($expectedTokenStream));
    }

    #[DataProvider('dataValidForTokenizer')]
    public function testInputStreamIsCorrectlyTokenized($inputText, $expectedTokenStream)
    {
        $fs = new FileSystem();
        $fs->createFile('/test.typoscript', $inputText);
        $tokenStream = $this->tokenizer->tokenizeStream($fs->path('/test.typoscript'));
        assertThat($tokenStream, equalTo($expectedTokenStream));
    }

    #[DataProvider('dataInvalidForTokenizer')]
    public function testInvalidInputTestThrowsTokenizerError($inputText)
    {
        $this->expectException(TokenizerException::class);
        $this->tokenizer->tokenizeString($inputText);
    }
}
