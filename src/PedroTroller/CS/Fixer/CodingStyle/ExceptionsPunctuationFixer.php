<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\CodingStyle;

use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class ExceptionsPunctuationFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_THROW);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSampleCode()
    {
        return <<<'PHP'
<?php

use LogicException;

class MyClass {
    public function fun1()
    {
        throw new \Exception('This is the message');
    }

    public function fun2($data)
    {
        throw new LogicException(sprintf('This is the %s', 'message'));
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentation()
    {
        return 'Exception messages MUST ends by ".", "…", "?" or "!".<br /><br /><i>Risky: will change the exception message.</i>';
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        $cases = $this->analyze($tokens)->findAllSequences([
            [
                [T_THROW],
                [T_NEW],
                [T_STRING],
                '(',
                [T_CONSTANT_ENCAPSED_STRING],
                ',',
            ],
            [
                [T_THROW],
                [T_NEW],
                [T_NS_SEPARATOR],
                [T_STRING],
                '(',
                [T_CONSTANT_ENCAPSED_STRING],
                ',',
            ],
            [
                [T_THROW],
                [T_NEW],
                [T_STRING],
                '(',
                [T_STRING, 'sprintf'],
                '(',
                [T_CONSTANT_ENCAPSED_STRING],
                ',',
            ],
            [
                [T_THROW],
                [T_NEW],
                [T_NS_SEPARATOR],
                [T_STRING],
                '(',
                [T_STRING, 'sprintf'],
                '(',
                [T_CONSTANT_ENCAPSED_STRING],
                ',',
            ],
            [
                [T_THROW],
                [T_NEW],
                [T_STRING],
                '(',
                [T_CONSTANT_ENCAPSED_STRING],
                ')',
            ],
            [
                [T_THROW],
                [T_NEW],
                [T_NS_SEPARATOR],
                [T_STRING],
                '(',
                [T_CONSTANT_ENCAPSED_STRING],
                ')',
            ],
            [
                [T_THROW],
                [T_NEW],
                [T_STRING],
                '(',
                [T_STRING, 'sprintf'],
                '(',
                [T_CONSTANT_ENCAPSED_STRING],
                ')',
            ],
            [
                [T_THROW],
                [T_NEW],
                [T_NS_SEPARATOR],
                [T_STRING],
                '(',
                [T_STRING, 'sprintf'],
                '(',
                [T_CONSTANT_ENCAPSED_STRING],
                ')',
            ],
        ]);

        foreach ($cases as $case) {
            $keys = array_keys($case);
            array_pop($keys);
            array_pop($case);
            $tokens[end($keys)] = $this->cleanupMessage(end($case));
        }
    }

    /**
     * @return Token
     */
    private function cleanupMessage(Token $token)
    {
        $content     = $token->getContent();
        $chars       = str_split($content);
        $quotes      = array_shift($chars);
        $quotes      = array_pop($chars);
        $ponctuation = end($chars);

        switch ($ponctuation) {
            case '.':
            case '…':
            case '?':
            case '!':
                return $token;
        }

        return new Token([T_CONSTANT_ENCAPSED_STRING, sprintf('%s%s.%s', $quotes, implode($chars), $quotes)]);
    }
}
