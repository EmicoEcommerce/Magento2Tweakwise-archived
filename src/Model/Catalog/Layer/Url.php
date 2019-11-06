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
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\UrlStrategyFactory;
use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend\Http\Request as HttpRequest;
use Magento\Catalog\Api\CategoryRepositoryInterface;
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
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var ExportHelper
     */
    protected $exportHelper;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var UrlStrategyFactory
     */
    protected $urlStrategyFactory;

    /**
     * Builder constructor.
     *
     * @param UrlStrategyFactory $urlStrategyFactory
     * @param HttpRequest $request
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ExportHelper $exportHelper
     */
    public function __construct(
        UrlStrategyFactory $urlStrategyFactory,
        HttpRequest $request,
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
    protected function getUrlStrategy()
    {
        if (!$this->urlStrategy) {
            $this->urlStrategy = $this->urlStrategyFactory->create();
        }

        return $this->urlStrategy;
    }

    /**
     * @return FilterApplierInterface
     */
    protected function getFilterApplier()
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
    protected function getCategoryUrlStrategy()
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
     * @throws NoSuchEntityException
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
    public function getClearUrl(array $activeFilterItems)
    {
        return $this->getUrlStrategy()
            ->getClearUrl($this->request, $activeFilterItems);
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
    public function getSliderUrl(Item $item)
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