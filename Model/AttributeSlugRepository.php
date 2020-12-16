<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model;

use Emico\Tweakwise\Api\AttributeSlugRepositoryInterface;
use Emico\Tweakwise\Api\Data\AttributeSlugInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Emico\Tweakwise\Api\Data\AttributeSlugSearchResultsInterfaceFactory;
use Emico\Tweakwise\Model\ResourceModel\AttributeSlug as AttributeSlugResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Emico\Tweakwise\Model\ResourceModel\AttributeSlug\CollectionFactory;
use Emico\Tweakwise\Api\Data\AttributeSlugInterfaceFactory;

class AttributeSlugRepository implements AttributeSlugRepositoryInterface
{
    /**
     * @var AttributeSlugResource
     */
    protected $resource;

    /**
     * @var AttributeSlugSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var AttributeSlugInterfaceFactory
     */
    protected $entityFactory;

    /**
     * @param AttributeSlugResource $resource
     * @param AttributeSlugInterfaceFactory $entityFactory
     * @param CollectionFactory $collectionFactory
     * @param AttributeSlugSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        AttributeSlugResource $resource,
        AttributeSlugInterfaceFactory $entityFactory,
        CollectionFactory $collectionFactory,
        AttributeSlugSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->entityFactory = $entityFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CouldNotSaveException
     */
    public function save(AttributeSlugInterface $attributeSlug): AttributeSlugInterface
    {
        try {
            /** @var AttributeSlug $attributeSlug */
            $this->resource->save($attributeSlug);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the page: %1',
                $exception->getMessage()
            ));
        }
        return $attributeSlug;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AttributeSlugInterface $attributeSlug): bool
    {
        try {
            /** @var AttributeSlug $attributeSlug */
            $this->resource->delete($attributeSlug);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Page: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @param string $attribute
     * @return AttributeSlugInterface
     * @throws NoSuchEntityException
     */
    public function findByAttribute(string $attribute): AttributeSlugInterface
    {
        $attributeSlug = $this->entityFactory->create();
        /** @var AttributeSlug $attributeSlug */
        $this->resource->load($attributeSlug, $attribute);
        if (!$attributeSlug->getAttribute()) {
            throw new NoSuchEntityException(__('No slug found for attribute "%s".', $attribute));
        }
        return $attributeSlug;
    }
}
