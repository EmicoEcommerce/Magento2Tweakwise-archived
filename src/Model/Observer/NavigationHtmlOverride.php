<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Observer;

use Emico\Tweakwise\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\LayeredNavigation\Block\Navigation;

class NavigationHtmlOverride implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * NavigationHtmlOverride constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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

        if (!$this->config->isLayeredEnabled()) {
            return;
        }

        if ($this->config->getUseDefaultLinkRenderer()) {
            return;
        }

        $block->setTemplate('Emico_Tweakwise::product/navigation/view.phtml');
    }
}