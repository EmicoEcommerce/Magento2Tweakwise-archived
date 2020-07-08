<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Magento\Framework\ObjectManagerInterface;

class SuggestionTypeFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * SuggestionTypeFactory constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $suggestion
     * @return SuggestionTypeAbstract
     */
    public function createSuggestion(array $suggestion): SuggestionTypeAbstract
    {
        $type = $this->getSuggestionType($suggestion);
        /** @var SuggestionTypeAbstract $suggestionType */
        $suggestionType = $this->objectManager->create($type);
        return $suggestionType->setData($suggestion);
    }

    /**
     * @param array $suggestion
     * @return string
     */
    protected function getSuggestionType(array $suggestion)
    {
        if (isset($suggestion['navigationLink']['context']['searchterm'])) {
            return SuggestionTypeSearch::class;
        }
        // First check for hit on facetFilters as it is more specific
        if (isset($suggestion['navigationLink']['context']['facetFilters'])) {
            return SuggestionTypeFacet::class;
        }

        if (isset($suggestion['navigationLink']['context']['category'])) {
            return SuggestionTypeCategory::class;
        }

        return SuggestionTypeSearch::class;
    }
}
