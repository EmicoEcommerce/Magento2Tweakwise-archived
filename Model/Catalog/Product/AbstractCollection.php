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

abstract class AbstractCollection extends ProductCollection
{
    /**
     * @return int[]
     */
    abstract protected function getProductIds();

    /**
     * @return $this
     */
    protected function applyEntityIdFilter()
    {
        $productIds = $this->getProductIds();
        if (count($productIds) === 0) {
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
        $productIds = $this->getProductIds();

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

        $this->fixProductOrder();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        $this->load();
        return parent::getSize();
    }
}
