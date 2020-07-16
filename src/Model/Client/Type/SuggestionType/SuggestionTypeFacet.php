<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\QueryParameterStrategy;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\UrlStrategyFactory;
use Emico\TweakwiseExport\Model\Helper;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class SuggestionTypeFacet extends SuggestionTypeCategory
{
    /**
     * @var UrlStrategyFactory
     */
    private $urlStrategyFactory;

    /**
     * SuggestionTypeFacet constructor.
     * @param UrlStrategyFactory $urlStrategyFactory
     * @param UrlInterface $urlInstance
     * @param CategoryRepository $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param Helper $exportHelper
     * @param array $data
     */
    public function __construct(
        UrlStrategyFactory $urlStrategyFactory,
        UrlInterface $urlInstance,
        CategoryRepository $categoryRepository,
        StoreManagerInterface $storeManager,
        Helper $exportHelper,
        array $data = []
    ) {
        parent::__construct(
            $urlInstance,
            $categoryRepository,
            $storeManager,
            $exportHelper,
            $data
        );

        $this->urlStrategyFactory = $urlStrategyFactory;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        $categoryUrl = $this->getCategoryUrl();
        if (!$categoryUrl) {
            // This can happen when category id is equal to the root category.
            // in this case we need to forward to the search page with the facet preselected
            return '';
        }

        $categoryIds = $this->getCategoryIds();
        $categoryIds = implode('-', $categoryIds);
        $facets = $this->getFacets();

        /**
         * This should be handled by whatever implements
         * @see \Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface
         */
        $strategy = $this->urlStrategyFactory->create();
        if ($strategy instanceof QueryParameterStrategy) {
            $query = array_merge(
                $facets,
                [
                    QueryParameterStrategy::PARAM_CATEGORY => $categoryIds
                ]
            );
            return $this->urlInstance->getDirectUrl(
                $categoryUrl,
                [
                    '_query' => $query
                ]
            );
        }

        // Add facets here
        return $categoryUrl;
    }



    /**
     *
     */
    protected function getFacets()
    {
        $facets = $this->data['navigationLink']['context']['facetFilters'] ?: [];

        $keys = array_column($facets, 'key');
        $values = array_column($facets, 'values');

        return array_combine($keys, $values);
    }
}
