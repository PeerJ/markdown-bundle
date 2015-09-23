<?php

namespace peerj\MarkdownBundle\CommonMark;

use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
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

        $environment->addInlineParser(new SuperscriptParser());
        $environment->addInlineParser(new SubscriptParser());

        $environment->addInlineRenderer('peerj\MarkdownBundle\CommonMark\Superscript', new SuperscriptRenderer());
        $environment->addInlineRenderer('peerj\MarkdownBundle\CommonMark\Subscript', new SubscriptRenderer());

        $environment->mergeConfig($config);
        parent::__construct(new DocParser($environment), new HtmlRenderer($environment));
    }
}
