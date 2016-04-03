<?php
namespace Helmich\TypoScriptParser\Tokenizer;

class Tokenizer implements TokenizerInterface
{
    const TOKEN_WHITESPACE = ',^[ \t\n]+,s';
    const TOKEN_COMMENT_ONELINE = ',^(#|/)[^\n]*,';
    const TOKEN_COMMENT_MULTILINE_BEGIN = ',^/\*,';
    const TOKEN_COMMENT_MULTILINE_END = ',^\*/,';
    const TOKEN_CONDITION = ',^(\[(adminUser|browser|version|system|device|useragent|language|IP|hostname|applicationContext|hour|minute|month|year|dayofweek|dayofmonth|dayofyear|usergroup|loginUser|page\|[a-zA-Z0-9_]+|treeLevel|PIDinRootline|PIDupinRootline|compatVersion|globalVar|globalString|userFunc)\s*=\s(.*?)\](\|\||&&|$))+,';
    const TOKEN_CONDITION_ELSE = ',^\[else\],i';
    const TOKEN_CONDITION_END = ',^\[(global|end)\],i';

    const TOKEN_OBJECT_NAME = ',^(CASE|CLEARGIF|COA(?:_INT)?|COBJ_ARRAY|COLUMNS|CTABLE|EDITPANEL|FILES?|FLUIDTEMPLATE|FORM|HMENU|HRULER|TEXT|IMAGE|IMG_RESOURCE|IMGTEXT|LOAD_REGISTER|MEDIA|MULTIMEDIA|OTABLE|QTOBJECT|RECORDS|RESTORE_REGISTER|SEARCHRESULT|SVG|SWFOBJECT|TEMPLATE|USER(?:_INT)?|GIFBUILDER|[GT]MENU(?:_LAYERS)?|(?:G|T|JS|IMG)MENUITEM)$,';
    const TOKEN_OBJECT_ACCESSOR = ',^([a-zA-Z0-9_\-\\\\]+(?:\.[a-zA-Z0-9_\-\\\\]+)*)$,';
    const TOKEN_OBJECT_REFERENCE = ',^\.?([a-zA-Z0-9_\-\\\\]+(?:\.[a-zA-Z0-9_\-\\\\]+)*)$,';

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
        ([a-zA-Z0-9_\-\\\\]+(?:\.[a-zA-Z0-9_\-\\\\]+)*)  # Left value (object accessor)
        (\s*)                                            # Whitespace
        (=|:=|<=|<|>|\{|\()                              # Operator
        (\s*)                                            # More whitespace
        (.*?)                                            # Right value
    $,x';
    const TOKEN_INCLUDE_STATEMENT = ',^
        <INCLUDE_TYPOSCRIPT:\s+
        source="(?<type>FILE|DIR):(?<filename>[^"]+)"
        (?:\s+extensions="(?<extensions>[^"]+)")?
        \s*>
    $,x';
    
    /** @var Scanner */
    private $scanner;

    /**
     * Tokenizer constructor.
     *
     * @param Scanner $scanner
     */
    public function __construct(Scanner $scanner = null)
    {
        $this->scanner = $scanner ?: new Scanner;
    }
    
    /**
     * @param string $inputString
     * @throws TokenizerException
     * @return TokenInterface[]
     */
    public function tokenizeString($inputString)
    {
        $inputString = $this->preprocessContent($inputString);

        $tokens = new TokenStreamBuilder();

        $currentTokenType  = null;
        $currentTokenValue = '';

        $lines                   = explode("\n", $inputString);
        $currentLine             = 0;
        $multiLineTokenStartLine = 0;

        foreach ($lines as $line) {
            $currentLine++;
            if ($currentTokenType === TokenInterface::TYPE_COMMENT_MULTILINE) {
                if ($matches = $this->scanner->scan($line, self::TOKEN_WHITESPACE)) {
                    $currentTokenValue .= $matches[0];
                }

                if ($matches = $this->scanner->peek($line, self::TOKEN_COMMENT_MULTILINE_END)) {
                    $currentTokenValue .= $matches[0];
                    $tokens->append(TokenInterface::TYPE_COMMENT_MULTILINE, $currentTokenValue, $currentLine);

                    $currentTokenValue = null;
                    $currentTokenType  = null;
                } else {
                    $currentTokenValue .= $line;
                }
                continue;
            } elseif ($currentTokenType === TokenInterface::TYPE_RIGHTVALUE_MULTILINE) {
                if ($this->scanner->peek($line, ',^\s*\),')) {
                    $tokens->append(
                        TokenInterface::TYPE_RIGHTVALUE_MULTILINE,
                        rtrim($currentTokenValue),
                        $multiLineTokenStartLine
                    );

                    $currentTokenValue = null;
                    $currentTokenType  = null;
                } else {
                    $currentTokenValue .= $line . "\n";
                }
                continue;
            }

            if ($tokens->count() !== 0) {
                $tokens->append(TokenInterface::TYPE_WHITESPACE, "\n", $currentLine - 1);
            }

            if ($matches = $this->scanner->scan($line, self::TOKEN_WHITESPACE)) {
                $tokens->append(TokenInterface::TYPE_WHITESPACE, $matches[0], $currentLine);
            }

            if ($this->scanner->peek($line, self::TOKEN_COMMENT_MULTILINE_BEGIN)) {
                $currentTokenValue = $line;
                $currentTokenType  = TokenInterface::TYPE_COMMENT_MULTILINE;
                continue;
            }

            $simpleTokens = [
                self::TOKEN_COMMENT_ONELINE   => TokenInterface::TYPE_COMMENT_ONELINE,
                self::TOKEN_NESTING_END       => TokenInterface::TYPE_BRACE_CLOSE,
                self::TOKEN_CONDITION         => TokenInterface::TYPE_CONDITION,
                self::TOKEN_CONDITION_ELSE    => TokenInterface::TYPE_CONDITION_ELSE,
                self::TOKEN_CONDITION_END     => TokenInterface::TYPE_CONDITION_END,
                self::TOKEN_INCLUDE_STATEMENT => TokenInterface::TYPE_INCLUDE,
            ];

            foreach ($simpleTokens as $pattern => $type) {
                if ($matches = $this->scanner->scan($line, $pattern)) {
                    $tokens->append($type, $matches[0], $currentLine, $matches);
                    continue 2;
                }
            }

            if ($matches = $this->scanner->scan($line, self::TOKEN_OPERATOR_LINE)) {
                $tokens->append(TokenInterface::TYPE_OBJECT_IDENTIFIER, $matches[1], $currentLine);

                if ($matches[2]) {
                    $tokens->append(TokenInterface::TYPE_WHITESPACE, $matches[2], $currentLine);
                }

                switch ($matches[3]) {
                    case '=':
                    case ':=':
                    case '<':
                    case '<=':
                    case '>':
                        $tokens->append(
                            $this->getTokenTypeForBinaryOperator($matches[3]),
                            $matches[3],
                            $currentLine
                        );

                        if ($matches[4]) {
                            $tokens->append(TokenInterface::TYPE_WHITESPACE, $matches[4], $currentLine);
                        }

                        if (($matches[3] === '<' || $matches[3] === '<=') && preg_match(self::TOKEN_OBJECT_REFERENCE, $matches[5])) {
                            $tokens->append(
                                TokenInterface::TYPE_OBJECT_IDENTIFIER,
                                $matches[5],
                                $currentLine
                            );
                            break;
                        }

                        if (preg_match(self::TOKEN_OBJECT_NAME, $matches[5])) {
                            $tokens->append(
                                TokenInterface::TYPE_OBJECT_CONSTRUCTOR,
                                $matches[5],
                                $currentLine
                            );
                        } elseif (preg_match(self::TOKEN_OBJECT_MODIFIER, $matches[5], $subMatches)) {
                            $tokens->append(
                                TokenInterface::TYPE_OBJECT_MODIFIER,
                                $matches[5],
                                $currentLine,
                                $subMatches
                            );
                        } elseif (strlen($matches[5])) {
                            $tokens->append(
                                TokenInterface::TYPE_RIGHTVALUE,
                                $matches[5],
                                $currentLine
                            );
                        }

                        break;
                    case '{':
                        $tokens->append(TokenInterface::TYPE_BRACE_OPEN, $matches[3], $currentLine);
                        break;
                    case '(':
                        $currentTokenValue       = "";
                        $currentTokenType        = TokenInterface::TYPE_RIGHTVALUE_MULTILINE;
                        $multiLineTokenStartLine = $currentLine;
                        break;
                }

                continue;
            }

            if (strlen($line) === 0) {
                continue;
            }

            throw new TokenizerException('Cannot tokenize line "' . $line . '"', 1403084444, null, $currentLine);
        }

        if ($currentTokenType !== null) {
            throw new TokenizerException('Unterminated ' . $currentTokenType . '!', 1403084445, null, $currentLine);
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
            case '<=':
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

    private function preprocessContent($content)
    {
        // Replace CRLF with LF.
        $content = str_replace("\r\n", "\n", $content);

        // Remove trailing whitespaces.
        $lines   = explode("\n", $content);
        $lines   = array_map('rtrim', $lines);
        $content = implode("\n", $lines);

        return $content;
    }
}
