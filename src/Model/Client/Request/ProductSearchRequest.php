<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Request;

use Emico\Tweakwise\Model\Config;
use Emico\TweakwiseExport\Model\Helper;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;

/**
 * Class ProductSearchRequest
 * @package Emico\Tweakwise\Model\Client\Request
 */
class ProductSearchRequest extends ProductNavigationRequest
{
    /**
     * {@inheritDoc}
     */
    protected $path = 'navigation-search';

    /**
     * @var Config
     */
    protected $config;

    /**
     * ProductSearchRequest constructor.
     * @param Helper $helper
     * @param StoreManager $storeManager
     * @param Config $config
     */
    public function __construct(
        Helper $helper,
        StoreManager $storeManager,
        Config $config
    ) {
        parent::__construct($helper, $storeManager);
        $this->config = $config;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function setSearch(string $query)
    {
        $this->setParameter('tn_q', $query);
        $this->setDefaultCategory();
        $this->setSearchLanguage();
        return $this;
    }

    /**
     * Add default category when no `tn_cid` parameter has been set
     */
    protected function setDefaultCategory()
    {
        if ($this->getParameter('tn_cid') === null) {
            $rootCategoryId = $this->getStoreRootCategoryId() ?: 2;
            $this->addCategoryFilter($rootCategoryId);
        }
    }

    /**
     * @return int
     */
    protected function getStoreRootCategoryId()
    {
        $store = $this->getStore();
        if ($store instanceof Store) {
            return $store->getRootCategoryId();
        }

        return null;
    }

    /**
     * Set language parameter if available
     */
    protected function setSearchLanguage()
    {
        $language = $this->config->getSearchLanguage();
        if (!$language) {
            return;
        }

        $this->setParameter('tn_lang', $language);
    }
}