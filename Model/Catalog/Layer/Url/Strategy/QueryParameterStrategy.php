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
use Emico\Tweakwise\Model\Catalog\Layer\Url\StrategyHelper;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlModel;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Emico\Tweakwise\Model\Config as TweakwiseConfig;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\Request\Http as MagentoHttpRequest;
use Magento\Framework\Stdlib\CookieManagerInterface;

class QueryParameterStrategy implements UrlInterface, FilterApplierInterface, CategoryUrlInterface
{
    /**
     * Separator used in category tree urls
     */
    public const CATEGORY_TREE_SEPARATOR = '-';

    /**
     * Extra ignored page parameters
     */
    public const PARAM_MODE = 'product_list_mode';
    public const PARAM_CATEGORY = 'categorie';

    /**
     * Commonly used query parameters from headers
     */
    public const PARAM_LIMIT = 'product_list_limit';
    public const PARAM_ORDER = 'product_list_order';
    public const PARAM_PAGE = 'p';
    public const PARAM_SEARCH = 'q';

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
     * @var UrlModel
     */
    protected $url;

    /**
     * @var StrategyHelper
     */
    protected $strategyHelper;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var TweakwiseConfig
     */
    protected $tweakwiseConfig;

    /**
     * Magento constructor.
     *
     * @param UrlModel $url
     * @param StrategyHelper $strategyHelper
     * @param CookieManagerInterface $cookieManager
     * @param TweakwiseConfig $config
     */
    public function __construct(
        UrlModel $url,
        StrategyHelper $strategyHelper,
        CookieManagerInterface $cookieManager,
        TweakwiseConfig $config
    ) {
        $this->url = $url;
        $this->strategyHelper = $strategyHelper;
        $this->cookieManager = $cookieManager;
        $this->tweakwiseConfig = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getClearUrl(MagentoHttpRequest $request, array $activeFilterItems): string
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
     * @param MagentoHttpRequest $request
     * @param array $query
     * @return string
     */
    protected function getCurrentQueryUrl(MagentoHttpRequest $request, array $query)
    {
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = false;

        if ($originalUrl = $request->getQuery('__tw_original_url')) {
            $urlArray = explode('/', $originalUrl);
            $newOriginalUrl = '';
            foreach ($urlArray as $url) {
                $newOriginalUrl .= '/' . filter_var($url, FILTER_SANITIZE_ENCODED);
            }

            //check if string should start with an / to prevent double slashes later
            if (strpos(mb_substr($originalUrl, 0, 1), '/', ) === false) {
                $newOriginalUrl = mb_substr($newOriginalUrl, 1);
            }

            return $this->url->getDirectUrl($newOriginalUrl, $params);
        }
        return $this->url->getUrl('*/*/*', $params);
    }

    /**
     * Fetch current selected values
     *
     * @param MagentoHttpRequest $request
     * @param Item $item
     * @return string[]|string|null
     */
    protected function getRequestValues(MagentoHttpRequest $request, Item $item)
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
            }

            return null;
        }

        if ($settings->getIsMultipleSelect()) {
            if (!is_array($data)) {
                $data = [$data];
            }
            return array_map('strval', $data);
        }

        return (string) $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryFilterSelectUrl(MagentoHttpRequest $request, Item $item): string
    {
        $category = $this->strategyHelper->getCategoryFromItem($item);
        if (!$this->getSearch($request)) {
            $categoryUrl = $category->getUrl();
            $categoryUrlPath = \parse_url($categoryUrl, PHP_URL_PATH);

            $url = $this->url->getDirectUrl(
                trim($categoryUrlPath, '/'),
                [
                    '_query' => $this->getAttributeFilters($request)
                ]
            );

            $url = str_replace($this->url->getBaseUrl(), '', $url);

            return $url;
        }

        $urlKey = $item->getFilter()->getUrlKey();

        $value[] = $category->getId();
        /** @var Category|CategoryInterface $category */
        while ((int)$category->getParentId() !== 1) {
            $value[] = $category->getParentId();
            $category = $category->getParentCategory();
        }

        $value = implode(self::CATEGORY_TREE_SEPARATOR, array_reverse($value));

        $query = [$urlKey => $value];
        return $this->getCurrentQueryUrl($request, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryFilterRemoveUrl(MagentoHttpRequest $request, Item $item): string
    {
        $filter = $item->getFilter();
        $urlKey = $filter->getUrlKey();

        $query = [$urlKey => $filter->getCleanValue()];
        return $this->getCurrentQueryUrl($request, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSelectUrl(MagentoHttpRequest $request, Item $item): string
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
     * @param MagentoHttpRequest $request
     * @param Item[] $filters
     * @return string
     */
    public function buildFilterUrl(MagentoHttpRequest $request, array $filters = []): string
    {
        $attributeFilters = $this->getAttributeFilters($request);
        return $this->getCurrentQueryUrl($request, $attributeFilters);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeRemoveUrl(MagentoHttpRequest $request, Item $item): string
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
    protected function getCategoryFilters(MagentoHttpRequest $request)
    {
        $categories = $request->getQuery(self::PARAM_CATEGORY);
        $categories = explode(self::CATEGORY_TREE_SEPARATOR, $categories ?? '');
        $categories = array_map('intval', $categories);
        $categories = array_filter($categories);
        $categories = array_unique($categories);

        return $categories;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeFilters(MagentoHttpRequest $request)
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
    public function getSliderUrl(MagentoHttpRequest $request, Item $item): string
    {
        $query = [$item->getFilter()->getUrlKey() => '{{from}}-{{to}}'];

        return $this->getCurrentQueryUrl($request, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function apply(MagentoHttpRequest $request, ProductNavigationRequest $navigationRequest): FilterApplierInterface
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

        if ($page && (bool) $navigationRequest->getParameter('resetPagination') === false) {
            $navigationRequest->setPage($page);
        }

        $limit = $this->getLimit($request);
        if ($limit) {
            $navigationRequest->setLimit($limit);
        }

        // Add this only for ajax requests
        if ($this->tweakwiseConfig->isPersonalMerchandisingActive() && $request->isAjax()) {
            $profileKey = $this->cookieManager->getCookie(
                $this->tweakwiseConfig->getPersonalMerchandisingCookieName(),
                null
            );
            if ($profileKey) {
                $navigationRequest->setProfileKey($profileKey);
            }
        }

        $categories = $this->getCategoryFilters($request);

        if ($categories) {
            $navigationRequest->addCategoryPathFilter($categories);
        }

        $search = $this->getSearch($request);
        if ($navigationRequest instanceof ProductSearchRequest && $search) {
            /** @var ProductSearchRequest $navigationRequest */
            $navigationRequest->setSearch($search);
        }

        return $this;
    }

    /**
     * @param MagentoHttpRequest $request
     * @return string|null
     */
    protected function getSortOrder(MagentoHttpRequest $request)
    {
        return $request->getQuery(self::PARAM_ORDER);
    }

    /**
     * @param MagentoHttpRequest $request
     * @return int|null
     */
    protected function getPage(MagentoHttpRequest $request)
    {
        return $request->getQuery(self::PARAM_PAGE);
    }

    /**
     * @param MagentoHttpRequest $request
     * @return int|null
     */
    protected function getLimit(MagentoHttpRequest $request)
    {
        return $request->getQuery(self::PARAM_LIMIT);
    }

    /**
     * @param MagentoHttpRequest $request
     * @return string|null
     */
    protected function getSearch(MagentoHttpRequest $request)
    {
        return $request->getQuery(self::PARAM_SEARCH);
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
