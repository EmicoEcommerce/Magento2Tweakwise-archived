<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */
declare(strict_types=1);

namespace Emico\Tweakwise\Model\Client\Type;

/**
 * Class SuggestionGroupType
 * @package Emico\Tweakwise\Model\Client\Type
 *
 * @method string|null getName()
 */
class SuggestionGroupType extends Type
{
    /**
     * @param SuggestionType[]|array[] $suggestions
     * @return $this
     */
    public function setSuggestions(array $suggestions)
    {
        $suggestions = $this->normalizeArray($suggestions, 'suggestion');

        $values = [];
        foreach ($suggestions as $value) {
            if (!$value instanceof SuggestionType) {
                $value = new SuggestionType($value);
            }

            $values[] = $value;
        }

        $this->data['suggestions'] = $values;
        return $this;
    }
}
