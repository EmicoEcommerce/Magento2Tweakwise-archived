<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Autocomplete;

use Emico\Tweakwise\Model\Autocomplete\DataProvider\ProductItemFactory;
use Emico\Tweakwise\Model\Autocomplete\DataProvider\SuggestionItemFactory;
use Emico\Tweakwise\Model\Client;
use Emico\Tweakwise\Model\Client\Request\AutocompleteRequest;
use Emico\Tweakwise\Model\Client\RequestFactory;
use Emico\Tweakwise\Model\Client\Response\AutocompleteResponse;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Layer\Category\CollectionFilter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Magento\Search\Model\Autocomplete\ItemInterface;
use Magento\Search\Model\Query;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class DataProvider implements DataProviderInterface
{
    /**
     * @var ProductItemFactory
     */
    protected $productItemFactory;

    /**
     * @var SuggestionItemFactory
     */
    protected $suggestionItemFactory;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CollectionFilter
     */
    protected $collectionFilter;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * DataProvider constructor.
     *
     * @param ProductItemFactory $productItemFactory
     * @param SuggestionItemFactory $suggestionItemFactory
     * @param QueryFactory $queryFactory
     * @param RequestFactory $requestFactory
     * @param Client $client
     * @param ProductCollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param CollectionFilter $collectionFilter
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        ProductItemFactory $productItemFactory,
        SuggestionItemFactory $suggestionItemFactory,
        QueryFactory $queryFactory,
        RequestFactory $requestFactory,
        Client $client,
        ProductCollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        CollectionFilter $collectionFilter,
        CategoryRepository $categoryRepository
    )
    {
        $this->productItemFactory = $productItemFactory;
        $this->suggestionItemFactory = $suggestionItemFactory;
        $this->queryFactory = $queryFactory;
        $this->requestFactory = $requestFactory;
        $this->client = $client;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->collectionFilter = $collectionFilter;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return Category
     */
    protected function getCategory()
    {
        $store = $this->storeManager->getStore();
        $rootCategoryId = $store->getRootCategoryId();
        return $this->categoryRepository->get($rootCategoryId);
    }

    /**
     * @param AutocompleteResponse $response
     * @return ItemInterface[]
     */
    protected function getProductItems(AutocompleteResponse $response)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->setStore($this->storeManager->getStore());
        $this->collectionFilter->filter($productCollection, $this->getCategory());

        $result = [];
        foreach ($response->getProductIds() as $productId) {
            $product = $productCollection->getItemById($productId);
            if (!$product) {
                continue;
            }

            $result[] = $this->productItemFactory->create(['product' => $product]);
        }
        return $result;
    }

    /**
     * @param AutocompleteResponse $response
     * @return ItemInterface[]
     */
    protected function getSuggestionResult(AutocompleteResponse $response)
    {
        $result = [];
        foreach ($response->getSuggestions() as $suggestion) {
            $result[] = $this->suggestionItemFactory->create(['suggestion' => $suggestion]);
        }
        return $result;
    }

    /**
     * @return ItemInterface[]
     */
    public function getItems()
    {
        /** @var Query $query */
        $query = $this->queryFactory->get();
        $query = $query->getQueryText();

        /** @var AutocompleteRequest $request */
        $request = $this->requestFactory->create();
        /** @var Store $store */
        $store = $this->storeManager->getStore();
        $request->addCategoryFilter($store->getRootCategoryId());
        $request->setSearch($query);

        /** @var AutocompleteResponse $response */
        $response = $this->client->request($request);

        $suggestionResult = $this->getSuggestionResult($response);
        $productResult = $this->getProductItems($response);

        return $suggestionResult + $productResult;
    }
}