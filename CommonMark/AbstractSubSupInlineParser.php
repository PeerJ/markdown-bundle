<?php

namespace peerj\MarkdownBundle\CommonMark;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Util\RegexHelper;

/**
 *
 */
abstract class AbstractSubSupInlineParser extends AbstractInlineParser
{
    /**
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(InlineParserContext $inlineContext)
    {
        $cursor = $inlineContext->getCursor();
        $character = $cursor->getCharacter();

        $charBefore = $cursor->peek(-1);
        if ($charBefore === null) {
            $charBefore = "\n";
        }

        $cursor->advanceBy(1);

        $charAfter = $cursor->getCharacter();
        if ($charAfter === null) {
            $charAfter = "\n";
        }

        $afterIsWhitespace = preg_match('/\pZ|\s/u', $charAfter);
        $afterIsPunctuation = preg_match(RegexHelper::REGEX_PUNCTUATION, $charAfter);

        $beforeIsWhitespace = preg_match('/\pZ|\s/u', $charBefore);
        $beforeIsPunctuation = preg_match(RegexHelper::REGEX_PUNCTUATION, $charBefore);

        $canOpen = !$afterIsWhitespace &&
            !($afterIsPunctuation && !$beforeIsWhitespace && !$beforeIsPunctuation);

        $canClose = !$beforeIsWhitespace &&
            !($beforeIsPunctuation && !$afterIsWhitespace && !$afterIsPunctuation);

        $node = new Text($cursor->getPreviousText(), ['delim' => true]);
        $inlineContext->getContainer()->appendChild($node);

        $delimiter = new Delimiter($character, 1, $node, $canOpen, $canClose);
        $inlineContext->getDelimiterStack()->push($delimiter);

        return true;
    }
}
