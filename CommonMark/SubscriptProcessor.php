<?php

namespace peerj\MarkdownBundle\CommonMark;

/**
 *
 */
class SubscriptProcessor extends AbstractSubSupProcessor
{
    /**
     * @return string
     */
    public function getOpeningCharacter() : string
    {
        return '~';
    }

    /**
     * @return string
     */
    public function getClosingCharacter() : string
    {
        return '~';
    }

    /**
     * @return int
     */
    function getMinLength(): int
    {
        return 1;
    }

    /**
     * @return Subscript
     */
    protected function createElement()
    {
        return new Subscript();
    }
}
