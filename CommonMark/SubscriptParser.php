<?php

namespace peerj\MarkdownBundle\CommonMark;

/**
 * Parse ~…~ as subscript
 */
class SubscriptParser extends AbstractSubSupInlineParser
{
    /**
     * @return array
     */
    public function getCharacters()
    {
        return ['~'];
    }
}
