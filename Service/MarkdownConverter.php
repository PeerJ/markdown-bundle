<?php

namespace peerj\MarkdownBundle\Service;

use peerj\MarkdownBundle\CommonMark\CommonMarkPlusConverter;

class MarkdownConverter
{
    /**
     * @var CommonMarkPlusConverter
     */
    private $converter;

    /**
     *
     */
    public function __construct()
    {
        $this->converter = new CommonMarkPlusConverter();
    }

    /**
     * @param string $markdown CommonMark input
     *
     * @return string
     */
    public function renderBlock($markdown)
    {
        return $this->converter->convertToHtml($markdown);
    }

    /**
     * Remove newlines, render to HTML, then remove the enclosing <p></p>
     *
     * @param string $markdown CommonMark input
     *
     * @return string
     */
    public function renderInline($markdown)
    {
        $markdown = str_replace("\n", ' ', $markdown);

        $html = $this->renderBlock($markdown);

        return substr($html, 3, -5);
    }
}
