<?php

namespace Emico\Tweakwise\Observer;

use Emico\Tweakwise\Api\AttributeSlugRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class RemoveTweakwiseSlugsBeforeSaveAttribute implements ObserverInterface
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
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        foreach ($observer->getEvent()->getAttribute()->getOptions() as $option) {
            try {
                if (empty($option->getLabel()) || ctype_space($option->getLabel())) {
                    continue;
                }

                $this->attributeSlugRepository
                    ->delete($this->attributeSlugRepository->findByAttribute($option->getLabel()))
                ;
            } catch (NoSuchEntityException $exception) {}
        }
    }
}
