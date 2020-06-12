<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type;

/**
 * Class SuggestionTypeSuggestion
 * @package Emico\Tweakwise\Model\Client\Type
 *
 * @method string getMatch();
 * @method setGroup(string $group);
 */
class SuggestionTypeSuggestion extends Type
{
    /**
     * @param $navigationLink
     */
    public function setNavigationLink(array $navigationLink)
    {
        $navigationLink = $this->normalizeArray($navigationLink, 'navigationLink');
    }
}
