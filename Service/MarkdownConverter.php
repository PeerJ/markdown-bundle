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

    /** @var string */
    protected $idPrefix = '';

    /** @var array */
    protected $config = [];

    /**
     * @var HTMLPurifier
     */
    private $purifier;

    private $allowedBlock = [
        //'input[checkbox]',
        // a[name] is deprecated in HTML5,
        // however, if a[id] is prefixed then a:name is the fallback for href linked fragments to work as expected
        'a[href|id|title|rel|aria-hidden]',
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
        'a[href|rel]',
        'b',
        'strong',
        'i',
        'em',
        'del',
        'sup',
        'sub',
        'code',
    ];

    public function __construct()
    {
        $this->config = [];
        $this->idPrefix = '';
    }

    /**
     * Initializes the markdown converter. Options passed into at runtime from the twig markdown filters
     *
     * @param array $options
     */
    private function initConverter($options = [])
    {
        $this->setIdAttributePrefix($options);

        $this->setHeadingLinksExtentionConfig($options);

        $this->converter = new CommonMarkPlusConverter($this->config);
    }

    /**
     * @param $options
     */
    private function setHeadingLinksExtentionConfig($options)
    {
        if (isset($options['headingLinks']) && $options['headingLinks'] === true) {
            $this->config = [
                'heading_permalink' => [
                    'id_prefix' => $this->idPrefix, // prefix set by htmlpurifier flag
                    'title' => 'Link',
                    'symbol' => '#'
                ],
            ];
        }
    }

    /**
     * @param array $options
     */
    private function setIdAttributePrefix($options)
    {
        // Check if idPrefix to override any default. Empty string allowed to set to nothing
        if (isset($options['idPrefix'])) {
            $this->idPrefix = $options['idPrefix'];

            // check prefix does not start with an int (illegal char), and add a string if so.
            if (!ctype_alpha($this->idPrefix[0])) {
                $this->idPrefix = 'p' . $this->idPrefix;
            }

            if ($this->idPrefix != '') {
                $this->idPrefix .= '-';
            }
        }
    }

    private function setPurifier($allowed = null)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Doctype', 'HTML 4.01 Strict');
        $config->set('Core.LexerImpl', 'DirectLex');
        $config->set('Attr.EnableID', true);
        if ($this->idPrefix != '') {
            $config->set('Attr.IDPrefix', $this->idPrefix);
        }
        // allows deprecated use of a[name] in HTML5. Used with namespacing user content header ids
        $config->set('HTML.Attr.Name.UseCDATA', true);
        $config->set('Attr.AllowedRel', ['nofollow', 'noreferrer', 'noopener']);

        if (!$allowed) {
            $allowed = $this->allowedBlock;
        }

        $config->set('HTML.Allowed', implode(',', $allowed));

        if ($def = $config->getHTMLDefinition(true)) {
            $def->addAttribute('a', 'aria-hidden', 'Enum#true,false');
        }
        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * @param string $markdown CommonMark input
     * @param array  $filterOptions
     *
     * @return string
     */
    public function renderBlock($markdown, $filterOptions = [])
    {
        $markdown = trim($markdown);
        $this->initConverter($filterOptions);

        $html = $this->converter->convertToHtml($markdown);

        $this->setPurifier();
        $html = $this->purifier->purify($html);

        return trim($html);
    }

    /**
     * Basic block rendering. Allows line breaks, basic formatting, and some code, but not much else.
     * @param string $markdown CommonMark input
     * @param array  $filterOptions
     *
     * @return string
     */
    public function renderBasicBlock($markdown, $filterOptions = [])
    {
        $markdown = trim($markdown);
        $this->initConverter($filterOptions);

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
     * @param array  $filterOptions
     *
     * @return string
     */
    public function renderInline($markdown, $filterOptions = [])
    {
        $this->initConverter($filterOptions);

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
     * @param array  $filterOptions
     *
     * @return string
     */
    public function renderTitle($markdown, $filterOptions = [])
    {
        $this->initConverter($filterOptions);

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
     * @param array  $filterOptions
     *
     * @return string
     */
    public function renderText($markdown, $filterOptions = [])
    {
        $this->initConverter($filterOptions);

        $markdown = trim(str_replace("\n", ' ', $markdown));
        $html = $this->converter->convertToHtml($markdown);
        $this->setPurifier([]);
        $html = $this->purifier->purify($html);

        return trim($html);
    }
}
