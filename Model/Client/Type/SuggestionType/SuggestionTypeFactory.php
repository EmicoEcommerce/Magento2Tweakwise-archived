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
     * @param string|null $type
     * @return SuggestionTypeAbstract
     */
    public function createSuggestion(array $suggestion, string $type = null): SuggestionTypeAbstract
    {
        $type = $this->resolveClass($type);
        /** @var SuggestionTypeAbstract $suggestionType */
        $suggestionType = $this->objectManager->create($type);
        return $suggestionType->setData($suggestion);
    }

    /**
     * @param string $suggestionType
     * @return string
     */
    protected function resolveClass(string $suggestionType): string
    {
        if ($suggestionType === SuggestionTypeSearch::TYPE) {
            return SuggestionTypeSearch::class;
        }

        if ($suggestionType === SuggestionTypeCategory::TYPE) {
            return SuggestionTypeCategory::class;
        }

        if ($suggestionType === SuggestionTypeFacet::TYPE) {
            return SuggestionTypeFacet::class;
        }

        return SuggestionTypeSearch::class;
    }
}
