<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\UrlStrategyFactory;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface as TweakwiseUrlInterface;
use Emico\TweakwiseExport\Model\Helper;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class SuggestionTypeFacet extends SuggestionTypeCategory
{
    public const TYPE = 'FacetFilter';

    /**
     * @var UrlStrategyFactory
     */
    private $urlStrategyFactory;

    /**
     * SuggestionTypeFacet constructor.
     * @param UrlStrategyFactory $urlStrategyFactory
     * @param UrlInterface $url
     * @param CategoryRepository $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param Helper $exportHelper
     * @param array $data
     */
    public function __construct(
        UrlStrategyFactory $urlStrategyFactory,
        UrlInterface $url,
        CategoryRepository $categoryRepository,
        StoreManagerInterface $storeManager,
        Helper $exportHelper,
        array $data = []
    ) {
        parent::__construct(
            $categoryRepository,
            $storeManager,
            $url,
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
        try {
            $categoryUrl = $this->getCategoryUrl() ?: '';
        } catch (NoSuchEntityException $e) {
            return $this->getSearchUrl();
        }

        $facets = $this->getFacets();

        /**
         * This should be handled by whatever implements The tweakwise url interface
         * @see \Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface
         */
        $strategy = $this->urlStrategyFactory->create();

        // Add facets here
        return $categoryUrl;
    }

    /**
     * @return array
     */
    protected function getFacets(): array
    {
        $facets = $this->data['navigationLink']['context']['facetFilters'] ?: [];
        if (empty($facets)) {
            return [];
        }

        $keys = array_column($facets, 'key');
        $values = array_column($facets, 'values');

        return array_combine($keys, $values);
    }
}
