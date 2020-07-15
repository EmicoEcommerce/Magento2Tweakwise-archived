<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

/**
 * Interface SuggestionTypeInterface
 * @package Emico\Tweakwise\Model\Client\Type\SuggestionType
 */
interface SuggestionTypeInterface
{
    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return string
     */
    public function getName();
}
