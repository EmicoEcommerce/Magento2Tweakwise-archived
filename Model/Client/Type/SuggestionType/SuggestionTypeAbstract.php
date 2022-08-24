<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\QueryParameterStrategy;
use Emico\Tweakwise\Model\Client\Type\Type;
use Emico\TweakwiseExport\Model\Helper;
use Magento\Framework\UrlInterface;

/**
 * Class SuggestionTypeAbstract
 * @package Emico\Tweakwise\Model\Client\Type\SuggestionType
 */
abstract class SuggestionTypeAbstract extends Type implements SuggestionTypeInterface
{
    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var Helper
     */
    protected $exportHelper;

    /**
     * SuggestionTypeAbstract constructor.
     * @param UrlInterface $url
     * @param Helper $exportHelper
     * @param array $data
     */
    public function __construct(
        UrlInterface $url,
        Helper $exportHelper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->exportHelper = $exportHelper;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $match = $this->getMatch();
        /** @var string $category */
        $categoryName = $this->getCategoryName();
        $insertion = (string) __('in');

        return ($categoryName) ? "$match $insertion $categoryName" : $match;
    }

    /**
     * @return string
     */
    protected function getSearchUrl()
    {
        $query = [
            'q' => $this->getSearchTerm()
        ];

        $categoryIds = $this->getCategoryIds();

        if ($categoryIds) {
            $query[QueryParameterStrategy::PARAM_CATEGORY] = implode($categoryIds);
        }

        return $this->url->getUrl(
            'catalogsearch/result',
            [
                '_query' => $query
            ]
        );
    }

    /**
     * @return string
     */
    protected function getSearchTerm(): string
    {
        return $this->data['navigationLink']['context']['searchterm'] ?? $this->data['match'];
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
    protected function getParentCategory()
    {
        return $this->data['navigationLink']['context']['category']['parentName'] ?? '';
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
