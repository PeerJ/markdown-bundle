<?php

namespace peerj\MarkdownBundle\CommonMark;

/**
 *
 */
class SuperscriptProcessor extends AbstractSubSupProcessor
{
    /**
     * @return string
     */
    public function getOpeningCharacter() : string
    {
        return '^';
    }

    /**
     * @return string
     */
    public function getClosingCharacter() : string
    {
        return '^';
    }

    /**
     * @return int
     */
    function getMinLength(): int
    {
        return 1;
    }

    /**
     * @return Superscript
     */
    protected function createElement()
    {
        return new Superscript();
    }
}
