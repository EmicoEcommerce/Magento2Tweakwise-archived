<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */
namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\PathSlugStrategy;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\QueryParameterStrategy;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\UrlStrategyFactory;
use Emico\Tweakwise\Model\Config;
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
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        UrlStrategyFactory $urlStrategyFactory,
        UrlInterface $url,
        CategoryRepository $categoryRepository,
        StoreManagerInterface $storeManager,
        Helper $exportHelper,
        Config $config,
        array $data = []
    ) {
        parent::__construct(
            $categoryRepository,
            $storeManager,
            $url,
            $exportHelper,
            $config,
            $data,
        );

        $this->urlStrategyFactory = $urlStrategyFactory;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        $isSearch = false;
        try {
            $url = $this->getCategoryUrl();
            if (!$url) {
                $isSearch = true;
                $url = $this->getSearchUrl();
            }
        } catch (NoSuchEntityException $e) {
            $isSearch = true;
            $url = $this->getSearchUrl();
        }

        $facets = $this->getFacets();

        /**
         * This should be handled by whatever implements The tweakwise url interface
         * However that is not available for this data structure, as such it is sort of copied from the relevant classes.
         * @see \Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface
         */
        $strategy = $this->urlStrategyFactory->create();
        if ($isSearch || $strategy instanceof QueryParameterStrategy) {
            $query = http_build_query($facets);
            $queryJoin = strpos($url, '?') === false ? '?' : '&';
            return $url . $queryJoin . $query;
        }

        if ($strategy instanceof PathSlugStrategy) {
            ksort($facets);
            $path = '';
            foreach ($facets as $urlKey => $facetValues) {
                sort($facetValues);
                $facetValues = array_map(
                    static function ($facetValue) use ($urlKey) {
                        return $urlKey . '/' . $facetValue;
                    },
                    $facetValues
                );

                $path .= implode('/', $facetValues);
            }

            return rtrim($url, '/') . '/' . $path;
        }

        return $url;
    }

    /**
     * @return array
     */
    protected function getFacets(): array
    {
        $facets = $this->data['navigationLink']['context']['facetFilters'] ?? [];
        if (empty($facets)) {
            return [];
        }

        $keys = array_column($facets, 'key');
        $values = array_column($facets, 'values');
        $values = array_map('array_values', $values);

        return array_combine($keys, $values);
    }
}
