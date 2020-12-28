<?php

namespace peerj\MarkdownBundle\CommonMark;

use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\HtmlRenderer;
use peerj\MarkdownBundle\CommonMark\ExternalLink\ExternalLinkExtension;
use peerj\MarkdownBundle\CommonMark\HeadingPermalink\HeadingPermalinkRenderer;

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
        // Overrides default ExternalLink extension. Adds event priority on processor.
        $environment->addExtension(new ExternalLinkExtension());

        $config['external_link'] =
            [
                'internal_hosts' => ['peerj.com','staging.peerj.com', 'testing.peerj.com', 'localhost'],
                'open_in_new_window' => false,
                'html_class' => 'external-link',
                'nofollow' => 'external',
                'noopener' => 'external',
                'noreferrer' => 'external',
            ];

        $environment->addInlineParser(new SuperscriptParser());
        $environment->addDelimiterProcessor(new SuperscriptProcessor());
        $environment->addInlineRenderer(Superscript::class, new SuperscriptRenderer());

        $environment->addInlineParser(new SubscriptParser());
        $environment->addDelimiterProcessor(new SubscriptProcessor());
        $environment->addInlineRenderer(Subscript::class, new SubscriptRenderer());

        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        if (isset($config['heading_permalink'])) {
            $environment->addExtension(new HeadingPermalinkExtension());
            $environment->addInlineRenderer(HeadingPermalink::class, new HeadingPermalinkRenderer());
        }

        $environment->mergeConfig($config);
        parent::__construct(new DocParser($environment), new HtmlRenderer($environment));
    }
}
