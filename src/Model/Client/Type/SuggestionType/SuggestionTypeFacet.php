<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

class SuggestionTypeFacet extends SuggestionTypeCategory
{
    /**
     * @return string
     */
    public function getUrl(): string
    {
        $categoryUrl = parent::getUrl();
        if (!$categoryUrl) {
            return '';
        }

        return $categoryUrl;
    }
}
