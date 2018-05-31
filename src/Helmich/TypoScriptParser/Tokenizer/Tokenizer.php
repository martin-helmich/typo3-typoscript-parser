<?php
namespace Helmich\TypoScriptParser\Tokenizer;

use Helmich\TypoScriptParser\Tokenizer\Preprocessing\Preprocessor;
use Helmich\TypoScriptParser\Tokenizer\Preprocessing\StandardPreprocessor;

class Tokenizer implements TokenizerInterface
{
    const TOKEN_WHITESPACE = ',^[ \t\n]+,s';
    const TOKEN_COMMENT_ONELINE = ',^(#|/)[^\n]*,';
    const TOKEN_COMMENT_MULTILINE_BEGIN = ',^/\*,';
    const TOKEN_COMMENT_MULTILINE_END = ',^\*/,';
    const TOKEN_CONDITION = ',^(\[(adminUser|browser|version|system|device|useragent|language|IP|hostname|applicationContext|hour|minute|month|year|dayofweek|dayofmonth|dayofyear|usergroup|loginUser|page\|[a-zA-Z0-9_]+|treeLevel|PIDinRootline|PIDupinRootline|compatVersion|globalVar|globalString|userFunc|[a-zA-Z\\\]+)\s*=?\s*(.*?)\](\|\||&&|$))+,';
    const TOKEN_CONDITION_ELSE = ',^\[else\],i';
    const TOKEN_CONDITION_END = ',^\[(global|end)\],i';

    const TOKEN_OBJECT_NAME = ',^(CASE|CLEARGIF|COA(?:_INT)?|COBJ_ARRAY|COLUMNS|CTABLE|EDITPANEL|FILES?|FLUIDTEMPLATE|FORM|HMENU|HRULER|TEXT|IMAGE|IMG_RESOURCE|IMGTEXT|LOAD_REGISTER|MEDIA|MULTIMEDIA|OTABLE|QTOBJECT|RECORDS|RESTORE_REGISTER|SEARCHRESULT|SVG|SWFOBJECT|TEMPLATE|USER(?:_INT)?|GIFBUILDER|[GT]MENU(?:_LAYERS)?|(?:G|T|JS|IMG)MENUITEM)$,';
    const TOKEN_OBJECT_ACCESSOR = ',^([a-zA-Z0-9_\-\\\\:\$\{\}]+(?:\.[a-zA-Z0-9_\-\\\\:\$\{\}]+)*)$,';
    const TOKEN_OBJECT_REFERENCE = ',^\.?([a-zA-Z0-9_\-\\\\:\$\{\}]+(?:\.[a-zA-Z0-9_\-\\\\:\$\{\}]+)*)$,';

    const TOKEN_NESTING_START = ',^\{$,';
    const TOKEN_NESTING_END = ',^\}$,';

    const TOKEN_OBJECT_MODIFIER = ',^
        (?<name>[a-zA-Z0-9]+)  # Modifier name
        (?:\s)*
        \(
        (?<arguments>[^\)]*)   # Argument list
        \)
    $,x';
    const TOKEN_OPERATOR_LINE = ',^
        ([a-zA-Z0-9_\-\\\\:\$\{\}]+(?:\.[a-zA-Z0-9_\-\\\\:\$\{\}]+)*)  # Left value (object accessor)
        (\s*)                                                          # Whitespace
        (=<|=|:=|<|>|\{|\()                                            # Operator
        (\s*)                                                          # More whitespace
        (.*?)                                                          # Right value
    $,x';
    const TOKEN_INCLUDE_STATEMENT = ',^
        <INCLUDE_TYPOSCRIPT:\s*
        source="(?<type>FILE|DIR):(?<filename>[^"]+)"
        (?:\s+extensions="(?<extensions>[^"]+)")?
        \s*>
    $,x';
    const TOKEN_INCLUDE_NEW_STATEMENT = ',^
        @import\s+
        \'(?<filename>[^\']+)\'
    $,x';

    /** @var string */
    protected $eolChar;

    /** @var Preprocessor */
    protected $preprocessor;

    /**
     * Tokenizer constructor.
     *
     * @param string       $eolChar      Line ending to use for tokenizing.
     * @param Preprocessor $preprocessor Option to preprocess file contents before actual tokenizing
     */
    public function __construct($eolChar = "\n", Preprocessor $preprocessor = null)
    {
        if ($preprocessor === null) {
            $preprocessor = new StandardPreprocessor($eolChar);
        }

        $this->eolChar = $eolChar;
        $this->preprocessor = $preprocessor;
    }

    /**
     * @param string $inputString
     * @throws TokenizerException
     * @return TokenInterface[]
     */
    public function tokenizeString($inputString)
    {
        $inputString = $this->preprocessor->preprocess($inputString);

        $tokens = new TokenStreamBuilder();
        $state  = new MultilineTokenBuilder();

        $lines   = explode($this->eolChar, $inputString);
        $scanner = new Scanner($lines);

        foreach ($scanner as $line) {
            $column = 1;

            if ($this->tokenizeMultilineToken($tokens, $state, $line)) {
                continue;
            }

            if ($tokens->count() !== 0) {
                $tokens->append(TokenInterface::TYPE_WHITESPACE, $this->eolChar, $line->index() - 1);
                $column += 1;
            }

            if ($matches = $line->scan(self::TOKEN_WHITESPACE)) {
                $tokens->append(TokenInterface::TYPE_WHITESPACE, $matches[0], $line->index());
                $column += strlen($matches[0]);
            }

            if ($line->peek(self::TOKEN_COMMENT_MULTILINE_BEGIN)) {
                $state->startMultilineToken(TokenInterface::TYPE_COMMENT_MULTILINE, $line->value(), $line->index(), $column);
                continue;
            }

            if ($this->tokenizeSimpleStatements($tokens, $line) ||
                $this->tokenizeObjectOperation($tokens, $state, $line) ||
                strlen($line) === 0) {
                continue;
            }

            throw new TokenizerException('Cannot tokenize line "' . $line . '"', 1403084444, null, $line->index());
        }

        if ($state->currentTokenType() !== null) {
            throw new TokenizerException(
                'Unterminated ' . $state->currentTokenType() . '!',
                1403084445,
                null,
                count($lines) - 1
            );
        }

        return $tokens->build()->getArrayCopy();
    }

    /**
     * @param string $inputStream
     * @return TokenInterface[]
     */
    public function tokenizeStream($inputStream)
    {
        $content = file_get_contents($inputStream);
        return $this->tokenizeString($content);
    }

    /**
     * @param string $operator
     * @return string
     * @throws UnknownOperatorException
     */
    private function getTokenTypeForBinaryOperator($operator)
    {
        switch ($operator) {
            case '=':
                return TokenInterface::TYPE_OPERATOR_ASSIGNMENT;
            case '<':
                return TokenInterface::TYPE_OPERATOR_COPY;
            case '=<':
                return TokenInterface::TYPE_OPERATOR_REFERENCE;
            case ':=':
                return TokenInterface::TYPE_OPERATOR_MODIFY;
            case '>':
                return TokenInterface::TYPE_OPERATOR_DELETE;
        }
        // It should not be possible in any case to reach this point
        // @codeCoverageIgnoreStart
        throw new UnknownOperatorException('Unknown binary operator "' . $operator . '"!');
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $tokens
     * @param $matches
     * @param $currentLine
     * @throws UnknownOperatorException
     */
    private function tokenizeBinaryObjectOperation(TokenStreamBuilder $tokens, $matches, $currentLine)
    {
        $tokens->append(
            $this->getTokenTypeForBinaryOperator($matches[3]),
            $matches[3],
            $currentLine
        );

        if ($matches[4]) {
            $tokens->append(TokenInterface::TYPE_WHITESPACE, $matches[4], $currentLine);
        }

        if (($matches[3] === '<' || $matches[3] === '=<') && preg_match(self::TOKEN_OBJECT_REFERENCE, $matches[5])) {
            $tokens->append(
                TokenInterface::TYPE_OBJECT_IDENTIFIER,
                $matches[5],
                $currentLine
            );
            return;
        }

        if ($matches[3] == ':=' && preg_match(self::TOKEN_OBJECT_MODIFIER, $matches[5], $subMatches)) {
            $tokens->append(
                TokenInterface::TYPE_OBJECT_MODIFIER,
                $matches[5],
                $currentLine,
                $subMatches
            );
            return;
        }

        if (preg_match(self::TOKEN_OBJECT_NAME, $matches[5])) {
            $tokens->append(
                TokenInterface::TYPE_OBJECT_CONSTRUCTOR,
                $matches[5],
                $currentLine
            );
            return;
        }

        if ($matches[3] == '>' && preg_match(self::TOKEN_COMMENT_ONELINE, $matches[5])) {
            $tokens->append(
                TokenInterface::TYPE_COMMENT_ONELINE,
                $matches[5],
                $currentLine
            );
            return;
        }

        if (strlen($matches[5])) {
            $tokens->append(
                TokenInterface::TYPE_RIGHTVALUE,
                $matches[5],
                $currentLine
            );
            return;
        }
    }

    /**
     * @param TokenStreamBuilder    $tokens
     * @param MultilineTokenBuilder $state
     * @param ScannerLine           $line
     * @return bool
     */
    private function tokenizeMultilineToken(TokenStreamBuilder $tokens, MultilineTokenBuilder $state, ScannerLine $line)
    {
        if ($state->currentTokenType() === TokenInterface::TYPE_COMMENT_MULTILINE) {
            $this->tokenizeMultilineComment($tokens, $state, $line);
            return true;
        }

        if ($state->currentTokenType() === TokenInterface::TYPE_RIGHTVALUE_MULTILINE) {
            $this->tokenizeMultilineAssignment($tokens, $state, $line);
            return true;
        }

        return false;
    }

    /**
     * @param $line
     * @param $state
     * @param $tokens
     * @return void
     */
    private function tokenizeMultilineComment(
        TokenStreamBuilder $tokens,
        MultilineTokenBuilder $state,
        ScannerLine $line
    ) {
        if ($matches = $line->scan(self::TOKEN_WHITESPACE)) {
            $state->appendToToken($matches[0]);
        }

        if ($matches = $line->peek(self::TOKEN_COMMENT_MULTILINE_END)) {
            $token = $state->endMultilineToken($matches[0]);
            $tokens->appendToken($token);
            return;
        }

        $state->appendToToken($matches[0]);
    }

    /**
     * @param $tokens
     * @param $state
     * @param $line
     */
    private function tokenizeMultilineAssignment(
        TokenStreamBuilder $tokens,
        MultilineTokenBuilder $state,
        ScannerLine $line
    ) {
        if ($line->peek(',^\s*\),')) {
            $token = $state->endMultilineToken();
            $tokens->appendToken($token);
            return;
        }

        $state->appendToToken($line . "\n");
    }

    /**
     * @param TokenStreamBuilder $tokens
     * @param ScannerLine        $line
     * @return bool
     */
    private function tokenizeSimpleStatements(TokenStreamBuilder $tokens, ScannerLine $line)
    {
        $simpleTokens = [
            self::TOKEN_COMMENT_ONELINE       => TokenInterface::TYPE_COMMENT_ONELINE,
            self::TOKEN_NESTING_END           => TokenInterface::TYPE_BRACE_CLOSE,
            self::TOKEN_CONDITION_ELSE        => TokenInterface::TYPE_CONDITION_ELSE,
            self::TOKEN_CONDITION_END         => TokenInterface::TYPE_CONDITION_END,
            self::TOKEN_CONDITION             => TokenInterface::TYPE_CONDITION,
            self::TOKEN_INCLUDE_STATEMENT     => TokenInterface::TYPE_INCLUDE,
            self::TOKEN_INCLUDE_NEW_STATEMENT => TokenInterface::TYPE_INCLUDE_NEW,
        ];

        foreach ($simpleTokens as $pattern => $type) {
            if ($matches = $line->scan($pattern)) {
                $tokens->append($type, $matches[0], $line->index(), $matches);
                return true;
            }
        }

        return false;
    }

    /**
     * @param $tokens
     * @param $state
     * @param $line
     * @return bool
     */
    private function tokenizeObjectOperation(
        TokenStreamBuilder $tokens,
        MultilineTokenBuilder $state,
        ScannerLine $line
    ) {
        if ($matches = $line->scan(self::TOKEN_OPERATOR_LINE)) {
            $tokens->append(TokenInterface::TYPE_OBJECT_IDENTIFIER, $matches[1], $line->index());

            if ($matches[2]) {
                $tokens->append(TokenInterface::TYPE_WHITESPACE, $matches[2], $line->index());
            }

            $operators = ['=', ':=', '<', '<=', '>', '=<'];
            if (in_array($matches[3], $operators)) {
                $this->tokenizeBinaryObjectOperation($tokens, $matches, $line->index());
            } elseif ($matches[3] == '{') {
                $tokens->append(TokenInterface::TYPE_BRACE_OPEN, $matches[3], $line->index());
            } elseif ($matches[3] == '(') {
                $state->startMultilineToken(TokenInterface::TYPE_RIGHTVALUE_MULTILINE, '', $line->index(), $tokens->currentColumn());
            }
            return true;
        }
        return false;
    }
}
