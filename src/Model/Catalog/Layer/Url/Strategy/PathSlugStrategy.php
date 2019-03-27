<?php
/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy;


use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\RouteMatchingInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlModel;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
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
     * Magento constructor.
     *
     * @param UrlModel $url
     * @param Config $config
     * @param Resolver $layerResolver
     * @param UrlFinderInterface $urlFinder
     * @param FilterSlugManager $filterSlugManager
     */
    public function __construct(UrlModel $url, Config $config, Resolver $layerResolver, UrlFinderInterface $urlFinder, FilterSlugManager $filterSlugManager)
    {
        //@todo This must be done with setter injection somehow.
        $this->url = $url;
        $url->setConfig($config);
        $this->layerResolver = $layerResolver;
        $this->filterSlugManager = $filterSlugManager;
        $this->urlFinder = $urlFinder;
    }

    /**
     * Get url when selecting item
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getSelectFilter(HttpRequest $request, Item $item)
    {
        $filters = $this->getActiveFilters();
        $filters[] = $item;

        return $this->getBaseUrl() . $this->buildFilterSlugPath($filters);
    }

    /**
     * Get url when removing item from selecting
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getRemoveFilter(HttpRequest $request, Item $item)
    {
        $filters = $this->getActiveFilters();
        foreach ($filters as $key => $activeItem) {
            if ($activeItem === $item) {
                unset($filters[$key]);
            }
        }

        return $this->getBaseUrl() . $this->buildFilterSlugPath($filters);
    }

    /**
     * @param HttpRequest $request
     * @param Filter $filter
     * @return string
     */
    public function getSlider(HttpRequest $request, Filter $filter)
    {
        //@todo implement
        return 'implement';
    }

    /**
     * Fetch clear all items from url
     *
     * @param HttpRequest $request
     * @param Item[] $activeFilterItems
     * @return string
     */
    public function getClearUrl(HttpRequest $request, array $activeFilterItems)
    {
        return $this->getBaseUrl();
    }

    /**
     * Apply all attribute filters, category filters, sort order, page limit request parameters to navigation request
     *
     * @param HttpRequest $request
     * @param ProductNavigationRequest $navigationRequest
     * @return $this
     */
    public function apply(HttpRequest $request, ProductNavigationRequest $navigationRequest)
    {
        if (!$request instanceof MagentoHttpRequest) {
            return $this;
        }
        $filterPath = $request->getParam(self::REQUEST_FILTER_PATH);
        if (empty($filterPath)) {
            return $this;
        }

        $filterParts = explode('/', $filterPath);
        $facetKey = $filterParts[0];
        foreach ($filterParts as $i => $part) {
            if ($i % 2 === 0) {
                $facetKey = $part;
            } else {
                //@todo look up slug
                //$facetValue = $this->getSlugAttributeMapper()->getAttributeValueBySlug($part);
                $facetValue = $part;
                if (!empty($facetKey)) {
                    $navigationRequest->addAttributeFilter($facetKey, $facetValue);
                }
            }
        }

        //$navigationRequest->addAttributeFilter('size', 'l');
    }

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        $params['_current'] = false;
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
     * @return array|Item[]
     */
    protected function getActiveFilters(): array
    {
        $filters = $this->getLayer()->getState()->getFilters();
        if (!\is_array($filters)) {
            $filters = [];
        }
        return $filters;
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

        //@todo sort filters consistently

        $path = '';
        /** @var Item $activeItem */
        foreach ($filters as $activeItem) {
            $filter = $activeItem->getFilter();
            $facet = $filter->getFacet();
            $settings = $facet->getFacetSettings();

            $urlKey = $settings->getUrlKey();
            $slug = $this->filterSlugManager->getSlugForFilterItemAttribute($activeItem);
            $path .= $urlKey . '/' . $slug . '/';
        }

        return '/' . $path;
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
                UrlRewrite::ENTITY_TYPE => 'category',
                UrlRewrite::REQUEST_PATH => $pathsToCheck
            ]
        );

        if (\count($categoryRewrites) === 0) {
            return false;
        }

        /** @var UrlRewrite $rewrite */
        $rewrite = current($categoryRewrites);

        // Set the filter params part of the URL as a seperate request param, so we can apply filters later on
        $request->setParam('filter_path', str_replace(ltrim(',', $rewrite->getRequestPath()), '', $path));

        $request->setAlias(
            MagentoUrlInterface::REWRITE_REQUEST_PATH_ALIAS,
            $rewrite->getRequestPath()
        );
        $request->setPathInfo('/' . $rewrite->getTargetPath());
    }

    /**
     * @param $fullUriPath
     * @return array
     */
    protected function getPossibleCategoryPaths($fullUriPath): array
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