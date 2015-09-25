<?php

namespace peerj\MarkdownBundle\CommonMark;

/**
 *
 */
class SubscriptProcessor extends AbstractSubSupProcessor
{
    /**
     * @return array
     */
    protected function getCharacters()
    {
        return ['~'];
    }

    /**
     * @return Subscript
     */
    protected function createElement()
    {
        return new Subscript();
    }
}
