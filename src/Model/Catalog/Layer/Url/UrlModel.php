<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Url;

class UrlModel
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var Url
     */
    private $implementation;

    /**
     * UrlModel constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(ObjectManagerInterface $objectManager, ProductMetadataInterface $productMetadata)
    {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        return $this->getImplementation()->getUrl($routePath, $routeParams);
    }

    /**
     * @return Url
     */
    private function getImplementation()
    {
        if (!$this->implementation) {
            /* @var $version string e.g. "2.1.7" */
            $version = $this->productMetadata->getVersion();
            $this->implementation = version_compare($version, '2.2.0') >= 0 ?
                $this->objectManager->get(UrlModel\V22::class) :
                $this->objectManager->get(UrlModel\V21::class);
        }
        return $this->implementation;
    }
}