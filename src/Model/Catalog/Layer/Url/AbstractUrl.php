<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
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
    public function getClearUrl(HttpRequest $request, Filter $filter)
    {
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        if ($settings->getSource() == SettingsType::SOURCE_CATEGORY) {
            return '#';
        } else {
            return $this->getAttributeCleanUrl($request, $filter);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilters(HttpRequest $request, ProductNavigationRequest $navigationRequest)
    {

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
     * Get url when all attribute options are removed
     *
     * @param HttpRequest $request
     * @param Filter $filter
     * @return string
     */
    protected abstract function getAttributeCleanUrl(HttpRequest $request, Filter $filter);
}