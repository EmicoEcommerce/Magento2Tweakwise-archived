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
class ProductSearchRequest extends ProductNavigationRequest implements SearchRequestInterface
{
    use SearchRequestTrait;

    /**
     * {@inheritDoc}
     */
    protected $path = 'navigation-search';

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
}
