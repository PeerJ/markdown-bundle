<?php

namespace peerj\MarkdownBundle\Twig;

use peerj\MarkdownBundle\Service\MarkdownConverter;

/**
 *
 */
class MarkdownExtension extends \Twig_Extension
{
    /**
     * @var MarkdownConverter
     */
    private $markdownConverter;

    /**
     * MarkdownExtension constructor.
     *
     * @param MarkdownConverter $markdownConverter
     */
    public function __construct(MarkdownConverter $markdownConverter)
    {
        $this->markdownConverter = $markdownConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('markdown_block', [
                $this->markdownConverter,
                'renderBlock'
            ], [
                //'pre_escape' => 'html',
                'is_safe' => ['html']
            ]),

            new \Twig_SimpleFilter('markdown_inline', [
                $this->markdownConverter,
                'renderInline'
            ], [
                //'pre_escape' => 'html',
                'is_safe' => ['html']
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'markdown';
    }
}
