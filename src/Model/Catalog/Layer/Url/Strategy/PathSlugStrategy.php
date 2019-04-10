<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy;


use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\Url\CategoryUrlInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\RouteMatchingInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlModel;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Test\Handler\Category\CategoryInterface;
use Magento\Framework\App\ActionInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Zend\Http\Request as HttpRequest;
use Magento\Framework\App\Request\Http as MagentoHttpRequest;
use Magento\Framework\UrlInterface as MagentoUrlInterface;

class PathSlugStrategy implements UrlInterface, RouteMatchingInterface, FilterApplierInterface
{
    const REQUEST_FILTER_PATH = 'filter_path';

    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var FilterSlugManager
     */
    private $filterSlugManager;

    /**
     * @var UrlModel
     */
    private $url;

    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * @var Item[]
     */
    private $activeFilters;
    /**
     * @var QueryParameterStrategy
     */
    private $queryParameterStrategy;

    /**
     * Magento constructor.
     *
     * @param UrlModel $url
     * @param Config $config
     * @param Resolver $layerResolver
     * @param UrlFinderInterface $urlFinder
     * @param FilterSlugManager $filterSlugManager
     */
    public function __construct(
        UrlModel $url,
        Config $config,
        Resolver $layerResolver,
        UrlFinderInterface $urlFinder,
        FilterSlugManager $filterSlugManager,
        QueryParameterStrategy $queryParameterStrategy
    ) {
        //@todo This must be done with setter injection somehow.
        $this->url = $url;
        $url->setConfig($config);
        $this->layerResolver = $layerResolver;
        $this->filterSlugManager = $filterSlugManager;
        $this->urlFinder = $urlFinder;
        $this->queryParameterStrategy = $queryParameterStrategy;
    }

    /**
     * Get url when selecting item
     *
     * @param MagentoHttpRequest|HttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getAttributeSelectUrl(HttpRequest $request, Item $item): string
    {
        $filters = $this->getActiveFilters();
        $filters[] = $item;

        return $this->buildFilterUrl($request, $filters);
    }

    /**
     * Get url when removing item from selecting
     *
     * @param MagentoHttpRequest|HttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getAttributeRemoveUrl(HttpRequest $request, Item $item): string
    {
        $filters = $this->getActiveFilters();
        foreach ($filters as $key => $activeItem) {
            if ($activeItem === $item) {
                unset($filters[$key]);
            }
        }

        return $this->buildFilterUrl($request, $filters);
    }

    /**
     * @param MagentoHttpRequest|HttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getSliderUrl(HttpRequest $request, Item $item): string
    {
        $filters = $this->getActiveFilters();
        foreach ($filters as $key => $activeItem) {
            if ($activeItem->getFilter()->getUrlKey() === $item->getFilter()->getUrlKey()) {
                unset($filters[$key]);
            }
        }
        $item->getAttribute()->setValue('title', '{{from}}-{{to}}');
        $filters[] = $item;

        return $this->buildFilterUrl($request, $filters);
    }

    /**
     * Fetch clear all items from url
     *
     * @param MagentoHttpRequest|HttpRequest $request
     * @param Item[] $activeFilterItems
     * @return string
     */
    public function getClearUrl(HttpRequest $request, array $activeFilterItems): string
    {
        return $this->buildFilterUrl($request, []);
    }

    /**
     * Apply all attribute filters, category filters, sort order, page limit request parameters to navigation request
     *
     * @param HttpRequest $request
     * @param ProductNavigationRequest $navigationRequest
     * @return $this
     */
    public function apply(HttpRequest $request, ProductNavigationRequest $navigationRequest): FilterApplierInterface
    {
        // Order / pagination etc. is still done with query parameters. Also apply this using the queryParameter strategy
        $this->queryParameterStrategy->apply($request, $navigationRequest);

        if (!$request instanceof MagentoHttpRequest) {
            return $this;
        }
        $filterPath = $request->getParam(self::REQUEST_FILTER_PATH);
        if (empty($filterPath)) {
            return $this;
        }

        $filterPath = trim($filterPath, '/');
        $filterParts = explode('/', $filterPath);
        $facet = $filterParts[0];
        foreach ($filterParts as $i => $part) {
            if ($i % 2 === 0) {
                $facet = $part;
            } else {
                $attribute = $this->filterSlugManager->getAttributeBySlug($part);
                // No attribute found for slug, this can be a slider slug i.e. "0-40", fallback and just pass the raw data to tweakwise.
                // @todo Maybe we need some validation here if this is indeed a slider attribute. No idea how we can know this
                if (empty($attribute)) {
                    $attribute = $part;
                }
                $navigationRequest->addAttributeFilter($facet, $attribute);
            }
        }

        return $this;
    }

    /**
     * @return array|Item[]
     */
    protected function getActiveFilters(): array
    {
        if ($this->activeFilters !== null) {
            return $this->activeFilters;
        }

        $filters = $this->getLayer()->getState()->getFilters();
        if (!\is_array($filters)) {
            return [];
        }
        $this->activeFilters = $filters;
        return $this->activeFilters;
    }

    /**
     * @return string
     */
    public function getCurrentUrl(): string
    {
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_escape'] = false;
        return $this->url->getUrl('*/*/*', $params);
    }

    /**
     * @return Layer
     */
    protected function getLayer(): Layer
    {
        return $this->layerResolver->get();
    }

    /**
     * @param MagentoHttpRequest $request
     * @param array $filters
     * @return string
     */
    protected function buildFilterUrl(MagentoHttpRequest $request, array $filters = []): string
    {
        $currentUrl = $this->getCurrentUrl();

        $currentFilterPath = $request->getParam(self::REQUEST_FILTER_PATH);
        $newFilterPath = $this->buildFilterSlugPath($filters);

        if (empty($currentFilterPath)) {
            $urlParts = parse_url($currentUrl);
            $url = $urlParts['path'] . $newFilterPath;
            if (isset($urlParts['query'])) {
                $url .= '?' . $urlParts['query'];
            }
            return $url;
        }

        // Replace filter path in current URL with the new filter combination path
        return str_replace($currentFilterPath, $newFilterPath, $currentUrl);
    }

    /**
     * @param array $filters
     * @return string
     */
    protected function buildFilterSlugPath(array $filters): string
    {
        if (empty($filters)) {
            return '';
        }

        usort($filters, [$this, 'sortFilterItems']);

        $path = '';
        /** @var Item $filterItem */
        foreach ($filters as $filterItem) {
            $filter = $filterItem->getFilter();
            $urlKey = $filter->getUrlKey();
            $selectionType = $filter->getFacet()->getFacetSettings()->getSelectionType();
            if ($selectionType === SettingsType::SELECTION_TYPE_SLIDER) {
                $slug = $filterItem->getAttribute()->getTitle();
            } else {
                $slug = $this->filterSlugManager->getSlugForFilterItem($filterItem);
            }
            $path .= $urlKey . '/' . $slug . '/';
        }

        return '/' . $path;
    }

    /**
     * First sort filter items by facet and then on attribute
     *
     * @param Item $filterItemA
     * @param Item $filterItemB
     * @return int
     */
    protected function sortFilterItems(Item $filterItemA, Item $filterItemB): int
    {
        $facetA = $filterItemA->getFilter()->getUrlKey();
        $facetB = $filterItemB->getFilter()->getUrlKey();
        $attributeA = $filterItemA->getAttribute()->getTitle();
        $attributeB = $filterItemB->getAttribute()->getTitle();

        if ($facetA === $facetB) {
            return $attributeA > $attributeB ? 1 : -1;
        }

        return $facetA > $facetB ? 1 : -1;
    }

    /**
     * @param MagentoHttpRequest $request
     * @return bool|ActionInterface
     */
    public function match(MagentoHttpRequest $request)
    {
        $path = trim($request->getPathInfo(), '/');

        $pathsToCheck = $this->getPossibleCategoryPaths($path);
        $categoryRewrites = $this->urlFinder->findAllByData(
            [
                UrlRewrite::ENTITY_TYPE => ['category', 'landingpage'],
                UrlRewrite::REQUEST_PATH => $pathsToCheck
            ]
        );

        if (\count($categoryRewrites) === 0) {
            return false;
        }

        /** @var UrlRewrite $rewrite */
        $rewrite = current($categoryRewrites);

        // Set the filter params part of the URL as a seperate request param, so we can apply filters later on
        $request->setParam(self::REQUEST_FILTER_PATH, str_replace($rewrite->getRequestPath(), '', $path));

        $request->setAlias(
            MagentoUrlInterface::REWRITE_REQUEST_PATH_ALIAS,
            $path
        );
        $request->setPathInfo('/' . $rewrite->getTargetPath());
    }

    /**
     * @param string $fullUriPath
     * @return array
     */
    protected function getPossibleCategoryPaths(string $fullUriPath): array
    {
        $pathParts = explode('/', $fullUriPath);
        $lastPathPart = array_shift($pathParts);
        $paths[] = $lastPathPart;
        foreach ($pathParts as $i => $pathPart) {
            $lastPathPart .= '/' . $pathPart;
            $paths[] = $lastPathPart;
        }
        return array_reverse($paths);
    }
}