<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */
namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\QueryParameterStrategy;
use Emico\TweakwiseExport\Model\Helper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class SuggestionTypeCategory extends SuggestionTypeAbstract
{
    public const TYPE = 'Category';

    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlInstance;

    /**
     * SuggestionTypeCategory constructor.
     * @param CategoryRepository $categoryRepository Empty category model used to resolve urls
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $url
     * @param Helper $exportHelper
     * @param array $data
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        StoreManagerInterface $storeManager,
        UrlInterface $url,
        Helper $exportHelper,
        array $data = []
    ) {
        parent::__construct(
            $url,
            $exportHelper,
            $data
        );
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $match = $this->getMatch();
        /** @var string $category */
        $categoryName = $this->getCategoryName();

        return $categoryName ?: $match;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        try {
            return $this->getCategoryUrl() ?: '';
        } catch (NoSuchEntityException $e) {
            return $this->getSearchUrl();
        }
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getCategoryUrl(): string
    {
        $categoryIds = $this->getCategoryIds();
        if (empty($categoryIds)) {
            return '';
        }

        $categoryId = end($categoryIds);

        /** @var Category $category */

        if ($categoryId === $this->getStoreRootCategory()) {
            return '';
        }
        $category = $this->categoryRepository->get($categoryId);
        return $category->getUrl();
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getStoreRootCategory(): int
    {
        /** @var Store|StoreInterface $store */
        $store = $this->storeManager->getStore();
        return (int)$store->getRootCategoryId();
    }
}
