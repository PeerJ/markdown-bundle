<?php

namespace peerj\MarkdownBundle\CommonMark;

use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\HtmlRenderer;

/**
 * Converts CommonMark-compatible Markdown to HTML, plus some extras
 */
class CommonMarkPlusConverter extends Converter
{
    /**
     * Create a new commonmark converter instance.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $environment = Environment::createCommonMarkEnvironment();

        $environment->addExtension(new ExternalLinkExtension());

        $config = [
            'external_link' => [
                'internal_hosts' => ['peerj.com','staging.peerj.com', 'testing.peerj.com', 'localhost'],
                'open_in_new_window' => false,
                'html_class' => 'external-link',
                'nofollow' => 'external',
                'noopener' => 'external',
                'noreferrer' => 'external',
            ],
        ];

        $environment->addExtension(new HeadingPermalinkExtension());

        $environment->addInlineParser(new SuperscriptParser());
        $environment->addDelimiterProcessor(new SuperscriptProcessor());
        $environment->addInlineRenderer(Superscript::class, new SuperscriptRenderer());

        $environment->addInlineParser(new SubscriptParser());
        $environment->addDelimiterProcessor(new SubscriptProcessor());
        $environment->addInlineRenderer(Subscript::class, new SubscriptRenderer());

        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $environment->mergeConfig($config);
        parent::__construct(new DocParser($environment), new HtmlRenderer($environment));
    }
}
