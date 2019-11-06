<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy;

use Emico\Tweakwise\Exception\RuntimeException;
use Emico\Tweakwise\Exception\UnexpectedValueException;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Catalog\Layer\UrlFactory;
use Emico\Tweakwise\Model\Catalog\Layer\Url\CategoryUrlInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\RouteMatchingInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlModel;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Zend\Http\Request as HttpRequest;
use Magento\Framework\App\Request\Http as MagentoHttpRequest;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class PathSlugStrategy implements UrlInterface, RouteMatchingInterface, FilterApplierInterface, CategoryUrlInterface
{
    const REQUEST_FILTER_PATH = 'filter_path';

    const MEDIA_EXTENSIONS = [
        '.jpg',
        '.png',
        '.jpeg',
        '.webp',
        '.gif'
    ];

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
    private $magentoUrl;

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
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CurrentContext
     */
    private $currentContext;

    /**
     * Magento constructor.
     *
     * @param UrlModel $magentoUrl
     * @param UrlFactory $urlFactory
     * @param Resolver $layerResolver
     * @param UrlFinderInterface $urlFinder
     * @param FilterSlugManager $filterSlugManager
     * @param QueryParameterStrategy $queryParameterStrategy
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param CurrentContext $currentContext
     */
    public function __construct(
        UrlModel $magentoUrl,
        UrlFactory $urlFactory,
        Resolver $layerResolver,
        UrlFinderInterface $urlFinder,
        FilterSlugManager $filterSlugManager,
        QueryParameterStrategy $queryParameterStrategy,
        StoreManagerInterface $storeManager,
        Config $config,
        CurrentContext $currentContext
    ) {
        $this->magentoUrl = $magentoUrl;
        $this->layerResolver = $layerResolver;
        $this->filterSlugManager = $filterSlugManager;
        $this->urlFinder = $urlFinder;
        $this->queryParameterStrategy = $queryParameterStrategy;
        $this->urlFactory = $urlFactory;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->currentContext = $currentContext;
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
        $attribute = clone $item->getAttribute();
        $attribute->setValue('title', '{{from}}-{{to}}');
        $filters[] = new Item($item->getFilter(), $attribute, $this->urlFactory->create());

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
        return $this->buildFilterUrl($request);
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
                try {
                    $attribute = $this->filterSlugManager->getAttributeBySlug($part);
                } catch (UnexpectedValueException $exception) {
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
        return $this->magentoUrl->getUrl('*/*/*', $params);
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
    public function buildFilterUrl(MagentoHttpRequest $request, array $filters = []): string
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
            $facetSettings = $filter->getFacet()->getFacetSettings();
            if ($facetSettings->getSource() === SettingsType::SOURCE_CATEGORY) {
                continue;
            }

            if ($facetSettings->getSelectionType() === SettingsType::SELECTION_TYPE_SLIDER) {
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
        if ($this->skip($request)) {
            return false;
        }

        $path = trim($request->getPathInfo(), '/');
        $pathsToCheck = $this->getPossibleCategoryPaths($path);

        $rewriteFilterData = [
            UrlRewrite::ENTITY_TYPE => $this->getRewriteEntitiesToCheck(),
            UrlRewrite::REQUEST_PATH => $pathsToCheck
        ];
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $rewriteFilterData[UrlRewrite::STORE_ID] = $storeId;
        } catch (NoSuchEntityException $e) {
            // No implementation
        }

        $categoryRewrites = $this->urlFinder->findAllByData($rewriteFilterData);
        if (\count($categoryRewrites) === 0) {
            return false;
        }

        $sortByLongestMatch = function (UrlRewrite $rewrite1, UrlRewrite $rewrite2) {
            return
                \strlen($rewrite2->getRequestPath()) - \strlen($rewrite1->getRequestPath());
        };
        usort($categoryRewrites, $sortByLongestMatch);

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
     * @return string[]
     */
    public function getRewriteEntitiesToCheck(): array
    {
        return ['category'];
    }

    /**
     * We dont need to match on media urls
     *
     * @param MagentoHttpRequest $request
     * @return bool
     */
    protected function skip(MagentoHttpRequest $request): bool
    {
        $requestPath = $request->getPathInfo();
        foreach (self::MEDIA_EXTENSIONS as $fileExtention) {
            if (strpos($requestPath, $fileExtention, -\strlen($fileExtention)) !== false) {
                return true;
            }
        }

        return false;
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

    /**
     * @param HttpRequest $request
     * @param Item $item
     * @return mixed
     */
    public function getCategoryFilterSelectUrl(
        HttpRequest $request,
        Item $item
    ): string {
        return $this->queryParameterStrategy->getCategoryFilterSelectUrl($request, $item);
    }

    /**
     * @param HttpRequest $request
     * @param Item $item
     * @return mixed
     */
    public function getCategoryFilterRemoveUrl(
        HttpRequest $request,
        Item $item
    ): string {
        return $this->queryParameterStrategy->getCategoryFilterRemoveUrl($request, $item);
    }

    /**
     * Determine if this UrlInterface is allowed in the current context
     *
     * @return boolean
     */
    public function isAllowed(): bool
    {
        try {
            $context = $this->currentContext->getContext();
        } catch (RuntimeException $e) {
            return true;
        }

        return !$context->getRequest() instanceof ProductSearchRequest
            && !$this->config->getUseFormFilters();
    }
}