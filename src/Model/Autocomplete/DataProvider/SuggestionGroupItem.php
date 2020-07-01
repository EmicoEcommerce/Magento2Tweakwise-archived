<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Autocomplete\DataProvider;

use Emico\Tweakwise\Model\Client\Type\SuggestionTypeGroup;
use Magento\Search\Model\Autocomplete\ItemInterface;

class SuggestionGroupItem implements ItemInterface
{

    /**
     * @var SuggestionTypeGroup
     */
    protected $group;

    /**
     * SuggestionGroupItem constructor.
     * @param SuggestionTypeGroup $group
     */
    public function __construct(SuggestionTypeGroup $group)
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
                'title' => $suggestion->getName(),
                'url' => $suggestion->getUrl()
            ];
        }

        return $result;
    }
}
