<?php

namespace peerj\MarkdownBundle\CommonMark;

use League\CommonMark\ContextInterface;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;

class SubscriptParser extends AbstractInlineParser
{
    /**
     * @return array
     */
    public function getCharacters()
    {
        return ['~'];
    }

    /**
     * @param ContextInterface    $context
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(ContextInterface $context, InlineParserContext $inlineContext)
    {
        $cursor = $inlineContext->getCursor();

        // TODO: not if in superscript context?
        if ($m = $cursor->match('/^\~([^\~]+)\~/')) {
            $text = substr($m, 1, -1);
            $inline = new Subscript([new Text($text)]);
            $inlineContext->getInlines()->add($inline);

            return true;
        }

        return false;

    }
}
