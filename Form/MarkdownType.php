<?php

namespace peerj\MarkdownBundle\Form;

use Symfony\Component\Form\AbstractType;

class MarkdownType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'markdown';
    }
}
