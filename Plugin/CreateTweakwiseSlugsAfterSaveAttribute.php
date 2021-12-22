<?php

namespace Emico\Tweakwise\Plugin;

use Emico\Tweakwise\Api\AttributeSlugRepositoryInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\FilterSlugManager;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;

class CreateTweakwiseSlugsAfterSaveAttribute
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

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject
     */
    public function afterSave(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $result
    )
    {
        $this->filterSlugManager->createFilterSlugByAttributeOptions($result->getOptions());
    }
}
