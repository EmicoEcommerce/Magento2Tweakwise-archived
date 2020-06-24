<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Autocomplete\DataProvider;

use Emico\Tweakwise\Model\Client\Type\SuggestionGroupType;
use Magento\Search\Model\Autocomplete\ItemInterface;

class SuggestionGroupItem implements ItemInterface
{

    /**
     * @var SuggestionGroupType
     */
    protected $group;

    /**
     * SuggestionGroupItem constructor.
     * @param SuggestionGroupType $group
     */
    public function __construct(SuggestionGroupType $group)
    {
        $this->group = $group;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->group->getName() ?: '';
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [
            'type' => 'suggestion_group',
            'title' => $this->group->getName()
        ];
        foreach ($this->group->getSuggestions() as $suggestion) {
            $result['suggestions'][] = [
                'title' => $suggestion->getMatch(),
            ];
        }

        return $result;
    }
}
