<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy;


use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\RouteMatchingInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Config;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class UrlStrategyFactory
 * @package Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy
 */
class UrlStrategyFactory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Config $config
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    /**
     * Get the configured strategy for a given interface
     *
     * @param string $interface
     * @return UrlInterface|RouteMatchingInterface|FilterApplierInterface
     */
    public function create(string $interface = UrlInterface::class)
    {
        $urlStrategy = $this->config->getUrlStrategy();  //path of query
        $implementation = $this->objectManager->get($urlStrategy);

        if ($implementation instanceof UrlInterface
            && !$implementation->isAllowed()
        ) {
            return $this->objectManager->get($interface);
        }

        // Check if concrete implementation implements the given interface.
        // If not return preference in di.xml
        if (!in_array($interface, class_implements($implementation), true)) {
            return $this->objectManager->get($interface);
        }

        return $implementation;
    }
}