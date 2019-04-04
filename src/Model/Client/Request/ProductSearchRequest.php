<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Request;

use Magento\Store\Model\Store;

class ProductSearchRequest extends ProductNavigationRequest
{
    /**
     * {@inheritDoc}
     */
    protected $path = 'navigation-search';

    /**
     * @param string $query
     * @return $this
     */
    public function setSearch($query)
    {
        $this->setParameter('tn_q', $query);
        $this->setDefaultCategory();
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
}