<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Response\Suggestions;

use Emico\Tweakwise\Model\Client\Response;
use Emico\Tweakwise\Model\Client\Type\SuggestionGroupType;

class SuggestionsResponse extends Response
{
    /**
     * @param array $groups
     */
    public function setGroup(array $groups)
    {
        $groups = $this->normalizeArray($groups, 'group');

        $values = [];
        foreach ($groups as $group) {
            if (!$group instanceof SuggestionGroupType) {
                $group = new SuggestionGroupType($group);
            }

            $values[] = $group;
        }

        $this->data['groups'] = $values;
        return $this;
    }
}
