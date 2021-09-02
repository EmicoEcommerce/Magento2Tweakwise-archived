<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\Url\CategoryUrlInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\UrlStrategyFactory;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\TweakwiseExport\Model\Helper as ExportHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\Request\Http as MagentoHttpRequest;
use Magento\Framework\Exception\NoSuchEntityException;

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
    protected UrlInterface $urlStrategy;

    /**
     * @var FilterApplierInterface
     */
    protected FilterApplierInterface $filterApplier;

    /**
     * @var CategoryUrlInterface
     */
    protected CategoryUrlInterface $categoryUrlStrategy;

    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categoryRepository;

    /**
     * @var ExportHelper
     */
    protected ExportHelper $exportHelper;

    /**
     * @var MagentoHttpRequest
     */
    protected MagentoHttpRequest $request;

    /**
     * @var UrlStrategyFactory
     */
    protected UrlStrategyFactory $urlStrategyFactory;

    /**
     * Builder constructor.
     *
     * @param UrlStrategyFactory $urlStrategyFactory
     * @param MagentoHttpRequest $request
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ExportHelper $exportHelper
     */
    public function __construct(
        UrlStrategyFactory $urlStrategyFactory,
        MagentoHttpRequest $request,
        CategoryRepositoryInterface $categoryRepository,
        ExportHelper $exportHelper
    ) {
        $this->urlStrategyFactory = $urlStrategyFactory;
        $this->categoryRepository = $categoryRepository;
        $this->exportHelper = $exportHelper;
        $this->request = $request;
    }

    /**
     * @return UrlInterface
     */
    protected function getUrlStrategy(): UrlInterface
    {
        if (!$this->urlStrategy) {
            $this->urlStrategy = $this->urlStrategyFactory->create();
        }

        return $this->urlStrategy;
    }

    /**
     * @return FilterApplierInterface
     */
    protected function getFilterApplier(): FilterApplierInterface
    {
        if (!$this->filterApplier) {
            $this->filterApplier = $this->urlStrategyFactory
                ->create(FilterApplierInterface::class);
        }

        return $this->filterApplier;
    }

    /**
     * @return CategoryUrlInterface
     */
    protected function getCategoryUrlStrategy(): CategoryUrlInterface
    {
        if (!$this->categoryUrlStrategy) {
            $this->categoryUrlStrategy = $this->urlStrategyFactory
                ->create(CategoryUrlInterface::class);
        }

        return $this->categoryUrlStrategy;
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getSelectFilter(Item $item): string
    {
        $settings = $item
            ->getFilter()
            ->getFacet()
            ->getFacetSettings();

        if ($settings->getSource() === SettingsType::SOURCE_CATEGORY) {
            return $this->getCategoryUrlStrategy()
                ->getCategoryFilterSelectUrl($this->request, $item);
        }

        return $this->getUrlStrategy()->getAttributeSelectUrl($this->request, $item);
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoveFilter(Item $item): string
    {
        $settings = $item->getFilter()
            ->getFacet()
            ->getFacetSettings();

        if ($settings->getSource() === SettingsType::SOURCE_CATEGORY) {
            return $this->getCategoryUrlStrategy()
                ->getCategoryFilterRemoveUrl($this->request, $item);
        }

        return $this->getUrlStrategy()->getAttributeRemoveUrl($this->request, $item);
    }

    /**
     * @param Item[] $activeFilterItems
     * @return string
     */
    public function getClearUrl(array $activeFilterItems): string
    {
        return $this->getUrlStrategy()
            ->getClearUrl($this->request, $activeFilterItems);
    }

    /**
     * @param array $activeFilterItems
     * @return string
     */
    public function getFilterUrl(array $activeFilterItems): string
    {
        return $this->getUrlStrategy()
            ->buildFilterUrl($this->request, $activeFilterItems);
    }

    /**
     * @param ProductNavigationRequest $navigationRequest
     */
    public function apply(ProductNavigationRequest $navigationRequest)
    {
        $this->getFilterApplier()->apply($this->request, $navigationRequest);
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getSliderUrl(Item $item): string
    {
        return $this->getUrlStrategy()->getSliderUrl($this->request, $item);
    }

    /**
     * @param Item $item
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    protected function getCategoryFromItem(Item $item): CategoryInterface
    {
        $tweakwiseCategoryId = $item->getAttribute()->getAttributeId();
        $categoryId = $this->exportHelper->getStoreId($tweakwiseCategoryId);

        return $this->categoryRepository->get($categoryId);
    }
}
