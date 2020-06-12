<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type;

/**
 * Class SuggestionGroupType
 * @package Emico\Tweakwise\Model\Client\Type
 *
 * @method string|null getName()
 * @method SuggestionTypeSuggestion[] getSuggestions();
 */
class SuggestionGroupType extends Type
{
    /**
     * @param SuggestionTypeSuggestion[]|array[] $suggestions
     * @return $this
     */
    public function setSuggestions(array $suggestions)
    {
        $suggestions = $this->normalizeArray($suggestions, 'suggestion');

        $values = [];
        foreach ($suggestions as $value) {
            if (!$value instanceof SuggestionTypeSuggestion) {
                $value = new SuggestionTypeSuggestion($value);
            }

            $values[] = $value;
        }

        $this->data['suggestions'] = $values;
        return $this;
    }
}
