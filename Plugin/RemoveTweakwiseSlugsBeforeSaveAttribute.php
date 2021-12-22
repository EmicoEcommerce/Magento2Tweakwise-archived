<?php

namespace Emico\Tweakwise\Plugin;

use Emico\Tweakwise\Api\AttributeSlugRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;

class RemoveTweakwiseSlugsBeforeSaveAttribute
{
    /**
     * @var AttributeSlugRepositoryInterface
     */
    protected AttributeSlugRepositoryInterface $attributeSlugRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param AttributeSlugRepositoryInterface $attributeSlugRepository
     */
    public function __construct(
        AttributeSlugRepositoryInterface $attributeSlugRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->attributeSlugRepository = $attributeSlugRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject
     */
    public function beforeSave(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject
    )
    {
        foreach ($subject->getOptions() as $option) {
            try {
                $this->attributeSlugRepository->delete($this->attributeSlugRepository->findByAttribute());
            } catch (NoSuchEntityException $exception) {}
        }
    }
}
