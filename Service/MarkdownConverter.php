<?php

namespace peerj\MarkdownBundle\Service;

use HTMLPurifier;
use HTMLPurifier_Config;
use peerj\MarkdownBundle\CommonMark\CommonMarkPlusConverter;

/**
 *
 */
class MarkdownConverter
{
    /**
     * @var CommonMarkPlusConverter
     */
    private $converter;

    /**
     * @var HTMLPurifier
     */
    private $purifier;

    /**
     * @var array
     */
    private $allowed = [
        'a[href]',
        'b',
        'strong',
        'i',
        'em',
        'sup',
        'sub',
        'blockquote',
        'p',
        'br',
        'h1',
        'h2',
        'h3',
        'ol',
        'ul',
        'li',
        'pre',
        'code',
        'img[alt|src]',
        'table[summary]',
        'tbody',
        'td[abbr]',
        'tfoot',
        'th[abbr]',
        'thead',
        'tr',

    ];

    /**
     *
     */
    public function __construct()
    {
        $this->converter = new CommonMarkPlusConverter();

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Doctype', 'HTML 4.01 Strict');
        $config->set('Core.LexerImpl', 'DirectLex');
        $config->set('HTML.Allowed', implode(',', $this->allowed));

        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * @param string $markdown CommonMark input
     *
     * @return string
     */
    public function renderBlock($markdown)
    {
        $markdown = trim($markdown);

        $html = $this->converter->convertToHtml($markdown);

        $html = $this->purifier->purify($html);

        return trim($html);
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
        $markdown = trim(str_replace("\n", ' ', $markdown));

        $html = $this->renderBlock($markdown);

        return substr($html, 3, -4);
    }
}
