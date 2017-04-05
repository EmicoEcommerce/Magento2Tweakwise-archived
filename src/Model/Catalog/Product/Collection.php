<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Product;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Indexer\Product\Flat\State;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\ResourceModel\Helper as CatalogResourceHelper;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory as CollectionEntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Zend_Db_Select;

class Collection extends ProductCollection
{
    /**
     * @var NavigationContext
     */
    protected $navigationContext;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        CollectionEntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        EavConfig $eavConfig,
        ResourceConnection $resource,
        EavEntityFactory $eavEntityFactory,
        CatalogResourceHelper $resourceHelper,
        UniversalFactory $universalFactory,
        StoreManagerInterface $storeManager,
        Manager $moduleManager,
        State $catalogProductFlatState,
        ScopeConfigInterface $scopeConfig,
        OptionFactory $productOptionFactory,
        Url $catalogUrl,
        TimezoneInterface $localeDate,
        Session $customerSession,
        DateTime $dateTime,
        GroupManagementInterface $groupManagement,
        NavigationContext $navigationContext,
        AdapterInterface $connection = null
    )
    {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection
        );

        $this->navigationContext = $navigationContext;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function addCategoryFilter(Category $category)
    {
        $this->navigationContext->getRequest()->addCategoryFilter($category);
        return $this;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function addSearchFilter($query)
    {
        $request = $this->navigationContext->getRequest();
        if ($request instanceof ProductSearchRequest) {
            $request->setSearch($query);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function applyEntityIdFilter()
    {
        $response = $this->navigationContext->getResponse();
        $productIds = $response->getProductIds();
        if (count($productIds) == 0) {
            // Result should be none make sure we dont load any products
            $this->addFieldToFilter('entity_id', ['null' => true]);
        } else {
            $this->addFieldToFilter('entity_id', ['in' => $productIds]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function applyCollectionSizeValues()
    {
        $response = $this->navigationContext->getResponse();
        $properties = $response->getProperties();

        $this->_pageSize = $properties->getPageSize();
        $this->_curPage = $properties->getCurrentPage();
        $this->_totalRecords = $properties->getNumberOfItems();
        return $this;
    }

    /**
     * @return $this
     */
    protected function clearFilters()
    {
        $select = $this->getSelect();
        $select->setPart(Zend_Db_Select::WHERE, []);
        $this->_pageSize = null;
        $this->_curPage = null;
        return $this;
    }

    /**
     * @return $this
     */
    protected function fixProductOrder()
    {
        $response = $this->navigationContext->getResponse();
        $productIds = $response->getProductIds();

        $result = [];
        foreach ($productIds as $productId) {
            if (isset($this->_items[$productId])) {
                $result[$productId] = $this->_items[$productId];
            }
        }
        $this->_items = $result;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->clearFilters();
        $this->applyEntityIdFilter();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        $this->applyCollectionSizeValues();
        $this->fixProductOrder();

        return $this;
    }
}