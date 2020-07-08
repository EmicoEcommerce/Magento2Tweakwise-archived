<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\TweakwiseExport\Model\Helper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class SuggestionTypeCategory extends SuggestionTypeAbstract
{
    /**
     * @var Helper
     */
    private $exportHelper;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * SuggestionTypeCategory constructor.
     * @param CategoryRepository $categoryRepository Empty category model used to resolve urls
     * @param Helper $exportHelper
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        Helper $exportHelper,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($data);
        $this->categoryRepository = $categoryRepository;
        $this->exportHelper = $exportHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        $categoryId = $this->getCategoryId();
        try {
            // Skip root categories
            if (!$categoryId || in_array($categoryId, [1, $this->getStoreRootCategory()], true)) {
                return '';
            }
            /** @var Category $category */
            $category = $this->categoryRepository->get($categoryId);
            return $category->getUrl();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * @return integer|null
     */
    protected function getCategoryId()
    {
        $path = $this->data['navigationLink']['context']['category']['path'];
        if (!$path) {
            return null;
        }
        $path = explode('-', $path);
        $twCategoryId = end($path);

        return $this->exportHelper->getStoreId((int)$twCategoryId);
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getStoreRootCategory()
    {
        /** @var Store|StoreInterface $store */
        $store = $this->storeManager->getStore();
        return (int) $store->getRootCategoryId();
    }
}
