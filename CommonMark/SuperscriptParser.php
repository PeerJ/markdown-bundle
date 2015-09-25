<?php

namespace peerj\MarkdownBundle\CommonMark;

/**
 * Parse ^…^ as superscript
 */
class SuperscriptParser extends AbstractSubSupInlineParser
{
    /**
     * @return array
     */
    public function getCharacters()
    {
        return ['^'];
    }
}
