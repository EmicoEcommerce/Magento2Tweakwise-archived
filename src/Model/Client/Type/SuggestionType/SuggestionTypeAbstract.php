<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\Tweakwise\Model\Client\Type\Type;
use Emico\TweakwiseExport\Model\Helper;

/**
 * Class SuggestionTypeAbstract
 * @package Emico\Tweakwise\Model\Client\Type\SuggestionType
 */
abstract class SuggestionTypeAbstract extends Type implements SuggestionTypeInterface
{
    /**
     * @var Helper
     */
    protected $exportHelper;

    /**
     * SuggestionTypeAbstract constructor.
     * @param Helper $exportHelper
     * @param array $data
     */
    public function __construct(
        Helper $exportHelper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->exportHelper = $exportHelper;
    }

    /**
     * @return string
     */
    abstract public function getUrl();

    /**
     * @return string
     */
    public function getName()
    {
        /** @var string $searchTerm */
        $match = $this->getMatch();
        /** @var string $category */
        $categoryName = $this->getCategoryName();

        return ($categoryName) ? "$match $categoryName" : $match;
    }

    /**
     * @return string
     */
    protected function getCategoryName()
    {
        return $this->data['navigationLink']['context']['category']['name'] ?? '';
    }

    /**
     * @return string
     */
    protected function getMatch()
    {
        return $this->data['match'] ?? '';
    }

    /**
     * @return int[]
     */
    protected function getCategoryIds(): array
    {
        $twCategoryIds = $this->data['navigationLink']['context']['category']['path'] ?? null;
        if (!$twCategoryIds) {
            return [];
        }
        $twCategoryIds = explode('-', $twCategoryIds);

        $categoryIds = array_map(
            function ($twCategoryId) {
                return $this->exportHelper->getStoreId((int)$twCategoryId);
            },
            $twCategoryIds
        );

        return array_filter(
            $categoryIds,
            static function (int $categoryId) {
                return $categoryId !== 1;
            }
        );
    }
}
