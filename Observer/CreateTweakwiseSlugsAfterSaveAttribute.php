<?php

namespace Emico\Tweakwise\Observer;

use Emico\Tweakwise\Api\AttributeSlugRepositoryInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\FilterSlugManager;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class CreateTweakwiseSlugsAfterSaveAttribute implements ObserverInterface
{
    /**
     * @var FilterSlugManager
     */
    protected FilterSlugManager $filterSlugManager;

    /**
     * @param FilterSlugManager $filterSlugManager
     */
    public function __construct(
        FilterSlugManager $filterSlugManager
    )
    {
        $this->filterSlugManager = $filterSlugManager;
    }

    public function execute(Observer $observer)
    {
        $this->filterSlugManager
            ->createFilterSlugByAttributeOptions($observer->getEvent()->getAttribute()->getOptions())
        ;
    }
}
