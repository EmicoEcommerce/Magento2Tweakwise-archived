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
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlModel;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Magento\Catalog\Api\Data\CategoryInterface;
use Zend\Http\Request as HttpRequest;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Emico\TweakwiseExport\Model\Helper as ExportHelper;
use Magento\Catalog\Model\Layer\Resolver;


class QueryParameterStrategy implements UrlInterface, FilterApplierInterface, CategoryUrlInterface
{
    /**
     * Separator used in category tree urls
     */
    const CATEGORY_TREE_SEPARATOR = '-';

    /**
     * Extra ignored page parameters
     */
    const PARAM_MODE = 'product_list_mode';
    const PARAM_CATEGORY = 'categorie';

    /**
     * Commonly used query parameters from headers
     */
    const PARAM_LIMIT = 'product_list_limit';
    const PARAM_ORDER = 'product_list_order';
    const PARAM_PAGE = 'p';
    const PARAM_SEARCH = 'q';

    /**
     * Parameters to be ignored as attribute filters
     *
     * @var string[]
     */
    protected $ignoredQueryParameters = [
        self::PARAM_CATEGORY,
        self::PARAM_ORDER,
        self::PARAM_LIMIT,
        self::PARAM_MODE,
        self::PARAM_SEARCH,
    ];

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ExportHelper
     */
    private $exportHelper;

    /**
     * @var UrlModel
     */
    private $url;

    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * Magento constructor.
     *
     * @param UrlModel $url
     */
    public function __construct(
        UrlModel $url,
        CategoryRepositoryInterface $categoryRepository,
        ExportHelper $exportHelper,
        Resolver $layerResolver)
    {
        $this->url = $url;
        $this->categoryRepository = $categoryRepository;
        $this->exportHelper = $exportHelper;
        $this->layerResolver = $layerResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getClearUrl(HttpRequest $request, array $activeFilterItems): string
    {
        $query = [];
        /** @var Item $item */
        foreach ($activeFilterItems as $item) {
            $filter = $item->getFilter();

            $urlKey = $filter->getUrlKey();
            $query[$urlKey] = $filter->getCleanValue();
        }

        return $this->getCurrentQueryUrl($request, $query);
    }

    /**
     * @param array $query
     * @return string
     */
    protected function getCurrentQueryUrl(HttpRequest $request, array $query)
    {
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = false;

        if ($originalUrl = $request->getQuery('__tw_original_url')) {
            return $this->url->getDirectUrl($originalUrl, $params);
        }
        return $this->url->getUrl('*/*/*', $params);
    }

    /**
     * Fetch current selected values
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string[]|string|null
     */
    protected function getRequestValues(HttpRequest $request, Item $item)
    {
        $filter = $item->getFilter();
        $settings = $filter
            ->getFacet()
            ->getFacetSettings();

        $urlKey = $filter->getUrlKey();

        $data = $request->getQuery($urlKey);
        if (!$data) {
            if ($settings->getIsMultipleSelect()) {
                return [];
            } else {
                return null;
            }
        }

        if ($settings->getIsMultipleSelect()) {
            if (!is_array($data)) {
                $data = [$data];
            }
            return array_map('strval', $data);
        } else {
            return (string) $data;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryFilterSelectUrl(HttpRequest $request, Item $item): string
    {
        return $this->getCategoryFromItem($item)->getUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryFilterRemoveUrl(HttpRequest $request, Item $item): string
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->getCategoryFromItem($item);
        /** @var \Magento\Catalog\Model\Category $parentCategory */
        $parentCategory = $category->getParentCategory();
        if (!$parentCategory || !$parentCategory->getId() || \in_array($parentCategory->getId(), [1,2], false)) {
            return $category->getUrl();
        }
        return $parentCategory->getUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSelectUrl(HttpRequest $request, Item $item): string
    {
        $settings = $item
            ->getFilter()
            ->getFacet()
            ->getFacetSettings();
        $attribute = $item->getAttribute();

        $urlKey = $settings->getUrlKey();
        $value = $attribute->getTitle();

        $values = $this->getRequestValues($request, $item);

        if ($settings->getIsMultipleSelect()) {
            $values[] = $value;
            $values = array_unique($values);

            $query = [$urlKey => $values];
        } else {
            $query = [$urlKey => $value];
        }

        return $this->getCurrentQueryUrl($request, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeRemoveUrl(HttpRequest $request, Item $item): string
    {
        $filter = $item->getFilter();
        $settings = $filter->getFacet()->getFacetSettings();

        $urlKey = $settings->getUrlKey();

        if ($settings->getIsMultipleSelect()) {
            $attribute = $item->getAttribute();
            $value = $attribute->getTitle();
            $values = $this->getRequestValues($request, $item);

            $index = array_search($value, $values, false);
            if ($index !== false) {
                /** @noinspection OffsetOperationsInspection */
                unset($values[$index]);
            }

            $query = [$urlKey => $values];
        } else {
            $query = [$urlKey => $filter->getCleanValue()];
        }

        return $this->getCurrentQueryUrl($request, $query);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeFilters(HttpRequest $request)
    {
        $result = [];
        foreach ($request->getQuery() as $attribute => $value) {
            if (in_array(mb_strtolower($attribute), $this->ignoredQueryParameters, false)) {
                continue;
            }

            $result[$attribute] = $value;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getSliderUrl(HttpRequest $request, Item $item): string
    {
        $query = [$item->getFilter()->getUrlKey() => '{{from}}-{{to}}'];

        return $this->getCurrentQueryUrl($request, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function apply(HttpRequest $request, ProductNavigationRequest $navigationRequest): FilterApplierInterface
    {
        $attributeFilters = $this->getAttributeFilters($request);
        foreach ($attributeFilters as $attribute => $values) {
            if (!is_array($values)) {
                $values = [$values];
            }

            foreach ($values as $value) {
                $navigationRequest->addAttributeFilter($attribute, $value);
            }
        }

        $sortOrder = $this->getSortOrder($request);
        if ($sortOrder) {
            $navigationRequest->setOrder($sortOrder);
        }

        $page = $this->getPage($request);
        if ($page) {
            $navigationRequest->setPage($page);
        }

        $limit = $this->getLimit($request);
        if ($limit) {
            $navigationRequest->setLimit($limit);
        }

        $isSearchRequest = $navigationRequest instanceof ProductSearchRequest;
        $search = $this->getSearch($request);
        if ($search && $isSearchRequest) {
            /** @var ProductSearchRequest $navigationRequest */
            $navigationRequest->setSearch($search);
        }
        return $this;
    }

    /**
     * @param HttpRequest $request
     * @return string|null
     */
    protected function getSortOrder(HttpRequest $request)
    {
        return $request->getQuery(self::PARAM_ORDER);
    }

    /**
     * @param HttpRequest $request
     * @return int|null
     */
    protected function getPage(HttpRequest $request)
    {
        return $request->getQuery(self::PARAM_PAGE);
    }

    /**
     * @param HttpRequest $request
     * @return int|null
     */
    protected function getLimit(HttpRequest $request)
    {
        return $request->getQuery(self::PARAM_LIMIT);
    }

    /**
     * @param HttpRequest $request
     * @return string|null
     */
    protected function getSearch(HttpRequest $request)
    {
        return $request->getQuery(self::PARAM_SEARCH);
    }

    /**
     * @param Item $item
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCategoryFromItem(Item $item): CategoryInterface
    {
        $tweakwiseCategoryId = $item->getAttribute()->getAttributeId();
        $categoryId = $this->exportHelper->getStoreId($tweakwiseCategoryId);

        return $this->categoryRepository->get($categoryId);
    }

    /**
     * Determine if this UrlInterface is allowed in the current context
     *
     * @return boolean
     */
    public function isAllowed(): bool
    {
        return true;
    }
}
