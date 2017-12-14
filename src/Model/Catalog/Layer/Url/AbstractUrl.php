<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\Tweakwise\Model\Config;
use Emico\TweakwiseExport\Model\Helper as ExportHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\UrlInterface as MagentoUrl;
use Zend\Http\Request as HttpRequest;

abstract class AbstractUrl implements UrlInterface
{
    /**
     * Commonly used query parameters from headers
     */
    const PARAM_LIMIT = 'product_list_limit';
    const PARAM_ORDER = 'product_list_order';
    const PARAM_PAGE = 'p';
    const PARAM_SEARCH = 'q';

    /**
     * @var MagentoUrl
     */
    protected $url;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ExportHelper
     */
    protected $exportHelper;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Magento constructor.
     *
     * @param MagentoUrl $url
     * @param CategoryRepository $categoryRepository
     * @param ExportHelper $exportHelper
     * @param Config $config
     */
    public function __construct(MagentoUrl $url, CategoryRepository $categoryRepository, ExportHelper $exportHelper, Config $config)
    {
        $this->url = $url;
        $this->categoryRepository = $categoryRepository;
        $this->exportHelper = $exportHelper;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectFilter(HttpRequest $request, Item $item)
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        if ($settings->getSource() == SettingsType::SOURCE_CATEGORY) {
            $category = $this->getCategoryFromItem($item);
            return $this->getCategorySelectUrl($request, $item, $category);
        } else {
            return $this->getAttributeSelectUrl($request, $item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoveFilter(HttpRequest $request, Item $item)
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        if ($settings->getSource() == SettingsType::SOURCE_CATEGORY) {
            $category = $this->getCategoryFromItem($item);
            return $this->getCategoryRemoveUrl($request, $item, $category);
        } else {
            return $this->getAttributeRemoveUrl($request, $item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function apply(HttpRequest $request, ProductNavigationRequest $navigationRequest)
    {
        $categories = $this->getCategoryFilters($request);
        foreach ($categories as $categoryId) {
            $navigationRequest->addCategoryFilter($categoryId);
        }

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

        $search = $this->getSearch($request);
        if ($search && $navigationRequest instanceof ProductSearchRequest) {
            $navigationRequest->setSearch($search);
        }
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
     * @return Category
     */
    protected function getCategoryFromItem(Item $item)
    {
        $tweakwiseCategoryId = $item->getAttribute()->getAttributeId();
        $categoryId = $this->exportHelper->getStoreId($tweakwiseCategoryId);

        return $this->categoryRepository->get($categoryId);
    }

    /**
     * @param HttpRequest $request
     * @param Item $item
     * @param Category $category
     * @return string
     */
    protected function getCategorySelectUrl(HttpRequest $request, Item $item, Category $category)
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        if ($settings->getSelectionType() == SettingsType::SELECTION_TYPE_TREE) {
            return $this->getCategoryTreeSelectUrl($request, $item, $category);
        }

        if ($this->config->getCategoryAsLink()) {
            return $category->getUrl();
        }

        return $this->getCategoryFilterSelectUrl($request, $item, $category);
    }

    /**
     * @param HttpRequest $request
     * @param Item $item
     * @param Category $category
     * @return string
     */
    protected function getCategoryRemoveUrl(HttpRequest $request, Item $item, Category $category)
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        if ($settings->getSelectionType() == SettingsType::SELECTION_TYPE_TREE) {
            return $this->getCategoryTreeRemoveUrl($request, $item, $category);
        }

        if ($this->config->getCategoryAsLink()) {
            return $category->getParentCategory()->getUrl();
        }

        return $this->getCategoryFilterRemoveUrl($request, $item, $category);
    }

    /**
     * Get url when category item is selected
     *
     * @param HttpRequest $request
     * @param Item $item
     * @param Category $category
     * @return string
     */
    protected abstract function getCategoryTreeSelectUrl(HttpRequest $request, Item $item, Category $category);

    /**
     * Get url when category item is selected
     *
     * @param HttpRequest $request
     * @param Item $item
     * @param Category $category
     * @return string
     */
    protected abstract function getCategoryFilterSelectUrl(HttpRequest $request, Item $item, Category $category);

    /**
     * Get url when category item is removed from url
     *
     * @param HttpRequest $request
     * @param Item $item
     * @param Category $category
     * @return string
     */
    protected abstract function getCategoryTreeRemoveUrl(HttpRequest $request, Item $item, Category $category);

    /**
     * Get url when category item is removed from url
     *
     * @param HttpRequest $request
     * @param Item $item
     * @param Category $category
     * @return string
     */
    protected abstract function getCategoryFilterRemoveUrl(HttpRequest $request, Item $item, Category $category);

    /**
     * Get url when attribute option is selected
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    protected abstract function getAttributeSelectUrl(HttpRequest $request, Item $item);

    /**
     * Get url when attribute option is removed
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    protected abstract function getAttributeRemoveUrl(HttpRequest $request, Item $item);

    /**
     * Fetch a list of category ID's to filter
     * @param HttpRequest $request
     * @return int[]
     */
    protected abstract function getCategoryFilters(HttpRequest $request);

    /**
     * Fetches all filters that should be applied on Tweakwise Request. In the format
     *
     * [
     *     // Single select attributes
     *     'attribute' => 'value',
     *     // Multi select attributes
     *     'attribute' => ['value1', 'value2'],
     * ]
     * @param HttpRequest $request
     * @return array
     */
    protected abstract function getAttributeFilters(HttpRequest $request);
}