<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Observer;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Emico\Tweakwise\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\LayeredNavigation\Block\Navigation;

/**
 * Class NavigationHtmlOverride
 * @package Emico\Tweakwise\Model\Observer
 *
 * Change template of the navigation block.
 * Changing the template depends on configuration so this could not be done in layout, also since the original definition
 * of the block is a virtualType it could not be done in a plugin, hence the observer.
 */
class NavigationHtmlOverride implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CurrentContext
     */
    private $currentContext;

    /**
     * NavigationHtmlOverride constructor.
     *
     * @param Config $config
     * @param CurrentContext $currentContext
     */
    public function __construct(
        Config $config,
        CurrentContext $currentContext
    ) {
        $this->config = $config;
        $this->currentContext = $currentContext;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getData('block');
        if (!$block instanceof Navigation) {
            return;
        }

        if ($this->config->getUseDefaultLinkRenderer()) {
            return;
        }

        $searchEnabled = $this->config->isSearchEnabled();
        $navigationEnabled = $this->config->isLayeredEnabled();

        $isSearch = $this->currentContext->getRequest() instanceof ProductSearchRequest;
        $isNavigation = !$isSearch;

        if ($isSearch && $searchEnabled) {
            $block->setTemplate('Emico_Tweakwise::layer/view.phtml');
        }

        if ($isNavigation && $navigationEnabled) {
            $block->setTemplate('Emico_Tweakwise::layer/view.phtml');
        }
    }
}
