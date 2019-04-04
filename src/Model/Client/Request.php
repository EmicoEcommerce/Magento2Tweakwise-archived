<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client;

use Emico\TweakwiseExport\Model\Helper;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;

class Request
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Request constructor.
     *
     * @param Helper $helper
     * @param StoreManager $storeManager
     */
    public function __construct(Helper $helper, StoreManager $storeManager)
    {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getResponseType()
    {
        return Response::class;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = (string) $path;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPathSuffix()
    {
        return null;
    }

    /**
     * @param string $parameter
     * @param string $value
     * @param string $separator
     * @return $this
     */
    public function addParameter($parameter, $value, $separator = '|')
    {
        if (isset($this->parameters[$parameter])) {
            if ($value == null) {
                unset($this->parameters[$parameter]);
            } else {
                $this->parameters[$parameter] = $this->parameters[$parameter] . $separator . $value;
            }
        } else if ($value !== null) {
            $this->parameters[$parameter] = (string) $value;
        }

        return $this;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param string $parameter
     * @param string|null $value
     * @return $this
     */
    public function setParameter($parameter, $value)
    {
        if ($value === null) {
            unset($this->parameters[$parameter]);
        } else {
            $value = strip_tags($value);
            $this->parameters[$parameter] = (string) $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $parameter
     * @return mixed|null
     */
    public function getParameter($parameter)
    {
        if (isset($this->parameters[$parameter])) {
            return $this->parameters[$parameter];
        }

        return null;
    }

    /**
     * @param string $parameter
     * @return bool
     */
    public function hasParameter($parameter)
    {
        return isset($this->parameters[$parameter]);
    }

    /**
     * @param Category|int $category
     * @return $this
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Category) {
            $categoryId = $category->getId();
            $storeId = $category->getStoreId();
        } else {
            $categoryId = (int) $category;
            $storeId = (int) $this->getStoreId();
        }

        $tweakwiseId = $this->helper->getTweakwiseId($storeId, $categoryId);
        $this->setParameter('tn_cid', $tweakwiseId);
        return $this;
    }

    /**
     * @return StoreManagerInterface
     */
    protected function getStore()
    {
        try {
            return $this->storeManager->getStore();
        } catch (NoSuchEntityException $e) {
            // Chose to not implement a good catch as this will not happen in practice.
            return null;
        }
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        $store = $this->getStore();
        if ($store instanceof StoreInterface) {
            return $store->getId();
        }

        return null;
    }
}