<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */
namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\TweakwiseExport\Model\Helper as ExportHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class StrategyHelper
{
    /**
     * @var ExportHelper
     */
    private $exportHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * StrategyHelper constructor.
     * @param ExportHelper $exportHelper
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        ExportHelper $exportHelper,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->exportHelper = $exportHelper;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Item $item
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    public function getCategoryFromItem(Item $item): CategoryInterface
    {
        $tweakwiseCategoryId = $item->getAttribute()->getAttributeId();
        $categoryId = $this->exportHelper->getStoreId($tweakwiseCategoryId);

        return $this->categoryRepository->get($categoryId);
    }
}
