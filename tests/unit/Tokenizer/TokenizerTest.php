<?php

namespace Helmich\TypoScriptParser\Tests\Unit\Tokenizer;

use Helmich\TypoScriptParser\Tokenizer\Token;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use Helmich\TypoScriptParser\Tokenizer\TokenizerException;
use Helmich\TypoScriptParser\Tokenizer\TokenizerInterface;
use PHPUnit\Framework\TestCase;
use VirtualFileSystem\FileSystem;

class TokenizerTest extends TestCase
{
    /** @var TokenizerInterface */
    private $tokenizer;

    public function setUp(): void
    {
        $this->tokenizer = new Tokenizer();
    }

    public function dataValidForTokenizer()
    {
        yield ["foo = bar", [
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
            new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1, 5),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 6),
            new Token(Token::TYPE_RIGHTVALUE, "bar", 1, 7),
        ]];

        // assert that trailing whitespaces are simply ignored
        yield ["foo = bar ", [
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
            new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1, 5),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 6),
            new Token(Token::TYPE_RIGHTVALUE, "bar", 1, 7),
        ]];

        yield ["foo.bar = baz", [
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo.bar", 1, 1),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 8),
            new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1, 9),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 10),
            new Token(Token::TYPE_RIGHTVALUE, "baz", 1, 11),
        ]];

        yield ["foo.bar.baz = baz", [
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo.bar.baz", 1, 1),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 12),
            new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1, 13),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 14),
            new Token(Token::TYPE_RIGHTVALUE, "baz", 1, 15),
        ]];

        yield ["foo =< bar", [
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
            new Token(Token::TYPE_OPERATOR_REFERENCE, "=<", 1, 5),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 7),
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "bar", 1, 8),
        ]];

        yield ["foo > # Something", [
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
            new Token(Token::TYPE_OPERATOR_DELETE, ">", 1, 5),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 6),
            new Token(Token::TYPE_COMMENT_ONELINE, "# Something", 1, 7),
        ]];

        yield ["<INCLUDE_TYPOSCRIPT: source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">", [
            new Token(Token::TYPE_INCLUDE, "<INCLUDE_TYPOSCRIPT: source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">", 1, 1, [
                0          => "<INCLUDE_TYPOSCRIPT: source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">",
                'type'     => 'FILE',
                1          => 'FILE',
                'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                2          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                'optional' => '',
                3          => '',
            ]),
        ]];

        // https://github.com/martin-helmich/typo3-typoscript-parser/issues/30
        yield ["<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">", [
            new Token(Token::TYPE_INCLUDE, "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">", 1, 1, [
                0          => "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\">",
                'type'     => 'FILE',
                1          => 'FILE',
                'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                2          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                'optional' => '',
                3          => '',
            ]),
        ]];

        // https://github.com/martin-helmich/typo3-typoscript-lint/issues/63
        yield ["<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\">", [
            new Token(Token::TYPE_INCLUDE, "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\">", 1, 1, [
                0          => "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\">",
                'type'     => 'FILE',
                1          => 'FILE',
                'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                2          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                'optional' => 'extensions="typoscript"',
                3          => 'extensions="typoscript"',
            ]),
        ]];

        // https://github.com/martin-helmich/typo3-typoscript-lint/issues/63
        yield ["<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">", [
            new Token(Token::TYPE_INCLUDE, "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">", 1, 1, [
                0          => "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">",
                'type'     => 'FILE',
                1          => 'FILE',
                'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                2          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                'optional' => 'condition="YourVendor\YourPackage\YourCondition"',
                3          => 'condition="YourVendor\YourPackage\YourCondition"',
            ]),
        ]];

        // https://github.com/martin-helmich/typo3-typoscript-lint/issues/63
        yield ["<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">", [
            new Token(Token::TYPE_INCLUDE, "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">", 1, 1, [
                0          => "<INCLUDE_TYPOSCRIPT:source=\"FILE:EXT:foo/Configuration/TypoScript/setup.typoscript\" extensions=\"typoscript\" condition=\"YourVendor\YourPackage\YourCondition\">",
                'type'     => 'FILE',
                1          => 'FILE',
                'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                2          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                'optional' => 'extensions="typoscript" condition="YourVendor\YourPackage\YourCondition"',
                3          => 'extensions="typoscript" condition="YourVendor\YourPackage\YourCondition"',
            ]),
        ]];

        yield ["@import 'EXT:foo/Configuration/TypoScript/setup.typoscript'", [
            new Token(Token::TYPE_INCLUDE_NEW, "@import 'EXT:foo/Configuration/TypoScript/setup.typoscript'", 1, 1, [
                0          => "@import 'EXT:foo/Configuration/TypoScript/setup.typoscript'",
                'filename' => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
                1          => 'EXT:foo/Configuration/TypoScript/setup.typoscript',
            ]),
        ]];

        yield ["[globalVar = GP:L = 1]", [
            new Token(Token::TYPE_CONDITION, "[globalVar = GP:L = 1]", 1, 1, [
                0      => "[globalVar = GP:L = 1]",
                1      => "[globalVar = GP:L = 1]",
                2      => 'globalVar = GP:L = 1',
                'expr' => 'globalVar = GP:L = 1',
                3      => '',
            ]),
        ]];

        yield ['[siteLanguage("languageId") == "1"]', [
            new Token(Token::TYPE_CONDITION, '[siteLanguage("languageId") == "1"]', 1, 1, [
                0      => '[siteLanguage("languageId") == "1"]',
                1      => '[siteLanguage("languageId") == "1"]',
                2      => 'siteLanguage("languageId") == "1"',
                'expr' => 'siteLanguage("languageId") == "1"',
                3      => '',
            ]),
        ]];
    }

    public function dataInvalidForTokenizer()
    {
        // unterminated multiline assignment
        yield ["a (\nasdf"];

        // unterminated block comment
        yield ["/*\nhello world"];

        // invalid operators
        yield ["foo != bar"];
        yield ["foo *= bar"];
    }

    /**
     * @param $inputText
     * @param $expectedTokenStream
     * @dataProvider dataValidForTokenizer
     */
    public function testInputTextIsCorrectlyTokenized($inputText, $expectedTokenStream)
    {
        $tokenStream = $this->tokenizer->tokenizeString($inputText);
        assertThat($tokenStream, equalTo($expectedTokenStream));
    }

    /**
     * @param $inputText
     * @param $expectedTokenStream
     * @dataProvider dataValidForTokenizer
     */
    public function testInputStreamIsCorrectlyTokenized($inputText, $expectedTokenStream)
    {
        $fs = new FileSystem();
        $fs->createFile('/test.typoscript', $inputText);
        $tokenStream = $this->tokenizer->tokenizeStream($fs->path('/test.typoscript'));
        assertThat($tokenStream, equalTo($expectedTokenStream));
    }

    /**
     * @param $inputText
     * @dataProvider dataInvalidForTokenizer
     */
    public function testInvalidInputTestThrowsTokenizerError($inputText)
    {
        $this->expectException(TokenizerException::class);
        $this->tokenizer->tokenizeString($inputText);
    }
}