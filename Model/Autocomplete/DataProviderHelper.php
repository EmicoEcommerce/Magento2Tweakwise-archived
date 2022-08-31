<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */
namespace Emico\Tweakwise\Model\Autocomplete;

use Emico\Tweakwise\Model\Autocomplete\DataProvider\ProductItemFactory;
use Emico\Tweakwise\Model\Client\Response\AutocompleteProductResponseInterface;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Category\CollectionFilter;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Search\Model\Autocomplete\ItemInterface;
use Magento\Search\Model\Query;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;

class DataProviderHelper
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var CollectionFilter
     */
    protected $collectionFilter;

    /**
     * @var ProductItemFactory
     */
    protected $productItemFactory;

    /**
     * AutocompleteDataProvider constructor
     * @param Config $config
     * @param QueryFactory $queryFactory
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param HttpRequest $request
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CollectionFilter $collectionFilter
     * @param ProductItemFactory $productItemFactory
     */
    public function __construct(
        Config $config,
        QueryFactory $queryFactory,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        HttpRequest $request,
        ProductCollectionFactory $productCollectionFactory,
        CollectionFilter $collectionFilter,
        ProductItemFactory $productItemFactory
    ) {
        $this->config = $config;
        $this->queryFactory = $queryFactory;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->request = $request;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->collectionFilter = $collectionFilter;
        $this->productItemFactory = $productItemFactory;
    }

    /**
     * @return Query|mixed|string|null
     */
    public function getQuery()
    {
        /** @var Query $query */
        $query = $this->queryFactory->get();

        return $query->getQueryText();
    }

    /**
     * @return Category
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function getCategory()
    {
        $categoryId = (int)$this->request->getParam('cid');
        if ($categoryId && $this->config->isAutocompleteStayInCategory()) {
            try {
                return $this->categoryRepository->get($categoryId);
            } catch (NoSuchEntityException $e) {
            }
        }

        $store = $this->storeManager->getStore();
        $categoryId = $store->getRootCategoryId();
        return $this->categoryRepository->get($categoryId);
    }

    /**
     * @param AutocompleteProductResponseInterface $response
     * @return ItemInterface[]
     * @throws LocalizedException
     */
    public function getProductItems(AutocompleteProductResponseInterface $response)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->setStore($this->storeManager->getStore());
        $productCollection->addAttributeToFilter('entity_id', ['in' => $response->getProductIds()]);
        $productCollection->addFieldToFilter('visibility', ['in' => [
            Visibility::VISIBILITY_BOTH,
            Visibility::VISIBILITY_IN_SEARCH
        ]]);
        $this->collectionFilter->filter($productCollection, $this->getCategory());

        $result = [];
        foreach ($response->getProductData() as $item) {
            $product = $productCollection->getItemById($item['id']);
            
            if (!$product) {
                continue;
            }
            
            $product->setData('tweakwise_price', $item['tweakwise_price']);

            $result[] = $this->productItemFactory->create(['product' => $product]);
        }

        return $result;
    }
}
