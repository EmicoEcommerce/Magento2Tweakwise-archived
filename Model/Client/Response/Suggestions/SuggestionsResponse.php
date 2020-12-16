<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Response\Suggestions;

use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Response;
use Emico\Tweakwise\Model\Client\Type\SuggestionTypeGroup;
use Emico\Tweakwise\Model\Client\Type\SuggestionTypeGroupFactory;
use Emico\TweakwiseExport\Model\Helper;

/**
 * Class SuggestionsResponse
 * @package Emico\Tweakwise\Model\Client\Response\Suggestions
 *
 * @method SuggestionTypeGroup[] getGroups();
 */
class SuggestionsResponse extends Response
{
    /**
     * @var SuggestionTypeGroupFactory
     */
    protected $suggestionTypeGroupFactory;

    /**
     * SuggestionsResponse constructor.
     * @param SuggestionTypeGroupFactory $suggestionTypeGroupFactory
     * @param Helper $helper
     * @param Request $request
     * @param array|null $data
     */
    public function __construct(
        SuggestionTypeGroupFactory $suggestionTypeGroupFactory,
        Helper $helper,
        Request $request,
        array $data = null
    ) {
        $this->suggestionTypeGroupFactory = $suggestionTypeGroupFactory;
        parent::__construct($helper, $request, $data);
    }

    /**
     * @param array $groups
     */
    public function setGroup(array $groups)
    {
        $groups = $this->normalizeArray($groups, 'group');

        $values = [];
        foreach ($groups as $group) {
            if (!$group instanceof SuggestionTypeGroup) {
                $suggestionGroup = $this->suggestionTypeGroupFactory->create();
                $suggestionGroup->setData($group);
                $values[] = $suggestionGroup;
            } else {
                $values[] = $group;
            }
        }

        $this->data['groups'] = $values;
        return $this;
    }
}
