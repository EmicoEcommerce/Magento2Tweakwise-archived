<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type;

use Emico\Tweakwise\Model\Client\Type\SuggestionType\SuggestionTypeAbstract;
use Emico\Tweakwise\Model\Client\Type\SuggestionType\SuggestionTypeFactory;

/**
 * Class SuggestionGroupType
 * @package Emico\Tweakwise\Model\Client\Type
 *
 * @method string|null getName()
 * @method setName(string $name);
 * @method SuggestionTypeAbstract[] getSuggestions();
 */
class SuggestionTypeGroup extends Type
{
    /**
     * @var SuggestionTypeFactory
     */
    protected $suggestionTypeFactory;

    /**
     * SuggestionTypeGroup constructor.
     * @param SuggestionTypeFactory $suggestionTypeFactory
     * @param array $data
     */
    public function __construct(
        SuggestionTypeFactory $suggestionTypeFactory,
        array $data = []
    ) {
        $this->suggestionTypeFactory = $suggestionTypeFactory;
        parent::__construct($data);
    }

    /**
     * @param SuggestionTypeAbstract[]|array[] $suggestions
     * @return $this
     */
    public function setSuggestions(array $suggestions)
    {
        $suggestions = $this->normalizeArray($suggestions, 'suggestion');

        $values = [];
        foreach ($suggestions as $suggestion) {
            if (!$suggestion instanceof SuggestionTypeAbstract) {
                $suggestion = $this->suggestionTypeFactory->createSuggestion($suggestion);
            }

            $values[] = $suggestion;
        }

        $this->data['suggestions'] = $values;
        return $this;
    }
}
