<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Autocomplete\DataProvider;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext;
use Emico\Tweakwise\Model\Catalog\Product\CollectionFactory;
use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Magento\Search\Model\Autocomplete\ItemInterface;
use Magento\Search\Model\QueryFactory;

class ProductProvider implements DataProviderInterface
{
    /**
     * @var ProductItemFactory
     */
    protected $itemFactory;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var NavigationContext
     */
    protected $navigationContext;

    /**
     * DataProvider constructor.
     *
     * @param ProductItemFactory $itemFactory
     * @param QueryFactory $queryFactory
     * @param CollectionFactory $collectionFactory
     * @param NavigationContext $navigationContext
     * @internal param RequestFactory $requestFactory
     */
    public function __construct(ProductItemFactory $itemFactory, QueryFactory $queryFactory, CollectionFactory $collectionFactory, NavigationContext $navigationContext)
    {
        $this->itemFactory = $itemFactory;
        $this->queryFactory = $queryFactory;
        $this->collectionFactory = $collectionFactory;
        $this->navigationContext = $navigationContext;
    }

    /**
     * @return ItemInterface[]
     */
    public function getItems()
    {
        $query = $this->queryFactory->get()->getQueryText();

        /** @var \Emico\Tweakwise\Model\Catalog\Product\Collection $productCollection */
        $productCollection = $this->collectionFactory->create(['navigationContext' => $this->navigationContext]);
        $productCollection->addSearchFilter($query);
        $productCollection->addAttributeToSelect('name');
        $productCollection->addAttributeToSelect('small_image');

        $result = [];
        foreach ($productCollection as $product) {
            $result[] = $this->itemFactory->create(['product' => $product]);
        }
        return $result;
    }
}