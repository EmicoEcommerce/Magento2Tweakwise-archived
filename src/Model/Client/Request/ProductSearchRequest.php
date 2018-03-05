<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Request;

use Emico\TweakwiseExport\Model\Helper;
use Magento\Store\Model\StoreManager;

class ProductSearchRequest extends ProductNavigationRequest
{
    /**
     * {@inheritDoc}
     */
    protected $path = 'navigation-search';

    /**
     * Request constructor.
     *
     * @param Helper $helper
     * @param StoreManager $storeManager
     */
    public function __construct(Helper $helper, StoreManager $storeManager)
    {
        parent::__construct($helper, $storeManager);
        $this->addAttributeFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $this->addAttributeFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH);
    }

    /**
     * @param string $query
     * @return $this
     */
    public function setSearch($query)
    {
        $this->setParameter('tn_q', $query);
        return $this;
    }
}