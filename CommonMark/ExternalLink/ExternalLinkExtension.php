<?php

namespace peerj\MarkdownBundle\CommonMark\ExternalLink;

use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\ExternalLink\ExternalLinkProcessor;

final class ExternalLinkExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment)
    {
        // Adds lower priority to ensure autolinks have already been created before running the external processor
        $environment->addEventListener(DocumentParsedEvent::class, new ExternalLinkProcessor($environment), -1);
    }
}
