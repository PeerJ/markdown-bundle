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

    private $allowedBlock = [
        //'input[checkbox]',
        'a[href|id|title|aria-hidden|name]',
        'b',
        'strong',
        'i',
        'em',
        'del',
        'sup',
        'sub',
        'code',
        'pre',
        'blockquote',
        'p[id]',
        'br',
        'hr',
        'h1[id]',
        'h2[id]',
        'h3[id]',
        'h4[id]',
        'h5[id]',
        'h6[id]',
        'ol',
        'ul',
        'li',
        'img[alt|src|width|height|id]',
        'table[summary|id]',
        'tbody',
        'td[abbr]',
        'tfoot',
        'th[abbr]',
        'thead',
        'tr',
    ];

    /**
     * @var array
     */
    private $allowedTitle = [
        'i',
        'em',
        'del',
        'sup',
        'sub',
    ];

    /**
     * @var array
     */
    private $allowedInline = [
        'a[href|id|title|aria-hidden|name]',
        'b',
        'strong',
        'i',
        'em',
        'del',
        'sup',
        'sub',
        'code',
    ];

    /**
     *
     */
    public function __construct()
    {
        $this->converter = new CommonMarkPlusConverter();
    }

    private function setPurifier($allowed = null)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Doctype', 'HTML 4.01 Strict');
        $config->set('Core.LexerImpl', 'DirectLex');

        if (!$allowed) {
            $allowed = $this->allowedBlock;
        }

        $config->set('HTML.Allowed', implode(',', $allowed));
        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * @param string $markdown CommonMark input
     *
     * @return string
     */
    public function renderBlock($markdown, $allowed = null)
    {
        $markdown = trim($markdown);

        $html = $this->converter->convertToHtml($markdown);

        $this->setPurifier();
        $html = $this->purifier->purify($html);

        return trim($html);
    }

    /**
     * Basic block rendering. Allows line breaks, basic formatting, and some code, but not much else.
     * @param string $markdown CommonMark input
     *
     * @return string
     */
    public function renderBasicBlock($markdown, $allowed = null)
    {
        $markdown = trim($markdown);

        $html = $this->converter->convertToHtml($markdown);

        $allowed = $this->allowedInline;
        $allowed[] = 'p';

        $this->setPurifier($allowed);
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
        $html = $this->converter->convertToHtml($markdown);
        $this->setPurifier($this->allowedInline);
        $html = $this->purifier->purify($html);

        return trim($html);
    }

    /**
     * Remove newlines, render to HTML
     *
     * @param string $markdown CommonMark input
     *
     * @return string
     */
    public function renderTitle($markdown)
    {
        $markdown = trim(str_replace("\n", ' ', $markdown));
        $html = $this->converter->convertToHtml($markdown);
        $this->setPurifier($this->allowedTitle);
        $html = $this->purifier->purify($html);

        return trim($html);
    }

    /**
     * Removes all formatting, returning just text
     *
     * @param string $markdown CommonMark input
     *
     * @return string
     */
    public function renderText($markdown)
    {
        $markdown = trim(str_replace("\n", ' ', $markdown));
        $html = $this->converter->convertToHtml($markdown);
        $this->setPurifier([]);
        $html = $this->purifier->purify($html);

        return trim($html);
    }
}
