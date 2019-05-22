<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer;

use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\Url\CategoryUrlInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\UrlStrategyFactory;
use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Zend\Http\Request as HttpRequest;
use Emico\TweakwiseExport\Model\Helper as ExportHelper;

/**
 * Class Url will later implement logic to use implementation selected in configuration.
 *
 * @package Emico\Tweakwise\Model\Catalog\Layer
 */
class Url
{
    /**
     * @var UrlInterface
     */
    protected $urlStrategy;

    /**
     * @var FilterApplierInterface
     */
    protected $filterApplier;

    /**
     * @var CategoryUrlInterface
     */
    protected $categoryUrlStrategy;

    /**
     * @var HttpRequest
     */
    protected $request;
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var ExportHelper
     */
    private $exportHelper;
    /**
     * @var Config
     */
    private $config;

    /**
     * Builder constructor.
     *
     * @param UrlStrategyFactory $urlStrategyFactory
     * @param HttpRequest $request
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ExportHelper $exportHelper
     * @param Config $config
     */
    public function __construct(
        UrlStrategyFactory $urlStrategyFactory,
        HttpRequest $request,
        CategoryRepositoryInterface $categoryRepository,
        ExportHelper $exportHelper,
        Config $config
    ) {
        $this->urlStrategy = $urlStrategyFactory->create(UrlInterface::class);
        $this->filterApplier = $urlStrategyFactory->create(FilterApplierInterface::class);
        $this->categoryUrlStrategy = $urlStrategyFactory->create(CategoryUrlInterface::class);
        $this->request = $request;
        $this->categoryRepository = $categoryRepository;
        $this->exportHelper = $exportHelper;
        $this->config = $config;
    }

    /**
     * @param Item $item
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSelectFilter(Item $item): string
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        if ($settings->getSource() === SettingsType::SOURCE_CATEGORY) {
            $category = $this->getCategoryFromItem($item);
            return $this->getCategorySelectUrl($item, $category);
        }

        return $this->urlStrategy->getAttributeSelectUrl($this->request, $item);
    }

    /**
     * @param Item $item
     * @param CategoryInterface $category
     * @return string
     */
    protected function getCategorySelectUrl(Item $item, CategoryInterface $category): string
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        if ($settings->getSelectionType() === SettingsType::SELECTION_TYPE_TREE) {
            return $this->categoryUrlStrategy->getCategoryTreeSelectUrl($this->request, $item, $category);
        }

        if ($this->config->getCategoryAsLink()) {
            return $category->getUrl();
        }

        return $this->categoryUrlStrategy->getCategoryFilterSelectUrl($this->request, $item, $category);
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoveFilter(Item $item): string
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        if ($settings->getSource() === SettingsType::SOURCE_CATEGORY) {
            $category = $this->getCategoryFromItem($item);
            return $this->getCategoryRemoveUrl($item, $category);
        }

        return $this->urlStrategy->getAttributeRemoveUrl($this->request, $item);
    }

    /**
     * @param Item $item
     * @param CategoryInterface $category
     * @return string
     */
    protected function getCategoryRemoveUrl(Item $item, CategoryInterface $category): string
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        if ($settings->getSelectionType() === SettingsType::SELECTION_TYPE_TREE) {
            return $this->categoryUrlStrategy->getCategoryTreeRemoveUrl($this->request, $item, $category);
        }

        if ($this->config->getCategoryAsLink()) {
            return $category->getParentCategory()->getUrl();
        }

        return $this->categoryUrlStrategy->getCategoryFilterRemoveUrl($this->request, $item, $category);
    }

    /**
     * @param Item[] $activeFilterItems
     * @return string
     */
    public function getClearUrl(array $activeFilterItems)
    {
        return $this->urlStrategy->getClearUrl($this->request, $activeFilterItems);
    }

    /**
     * @param ProductNavigationRequest $navigationRequest
     */
    public function apply(ProductNavigationRequest $navigationRequest)
    {
        $this->filterApplier->apply($this->request, $navigationRequest);
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getSliderUrl(Item $item)
    {
        return $this->urlStrategy->getSliderUrl($this->request, $item);
    }

    /**
     * @param Item $item
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCategoryFromItem(Item $item)
    {
        $tweakwiseCategoryId = $item->getAttribute()->getAttributeId();
        $categoryId = $this->exportHelper->getStoreId($tweakwiseCategoryId);

        return $this->categoryRepository->get($categoryId);
    }
}