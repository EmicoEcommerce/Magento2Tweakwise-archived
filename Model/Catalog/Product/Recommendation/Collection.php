<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Product\Recommendation;

use Emico\Tweakwise\Model\Catalog\Product\AbstractCollection;
use Emico\Tweakwise\Model\Client\Response\RecommendationsResponse;
use Magento\Catalog\Model\Indexer\Product\Flat\State;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\ResourceModel\Helper as CatalogResourceHelper;
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

class Collection extends AbstractCollection
{
    /**
     * @var RecommendationsResponse
     */
    protected $response;

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
        RecommendationsResponse $response,
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
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function getProductIds()
    {
        return $this->response->getProductIds();
    }
}
