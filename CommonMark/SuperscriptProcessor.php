<?php

namespace peerj\MarkdownBundle\CommonMark;

/**
 *
 */
class SuperscriptProcessor extends AbstractSubSupProcessor
{
    /**
     * @return array
     */
    protected function getCharacters()
    {
        return ['^'];
    }

    /**
     * @return Superscript
     */
    protected function createElement()
    {
        return new Superscript();
    }
}
