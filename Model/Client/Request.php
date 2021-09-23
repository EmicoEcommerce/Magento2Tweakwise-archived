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

class Request
{
    /**
     * @var string
     */
    protected string $path;

    /**
     * @var array
     */
    protected array $parameters = [];

    /**
     * @var StoreManager
     */
    protected StoreManager $storeManager;

    /**
     * @var Helper
     */
    protected Helper $helper;

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
    public function getResponseType(): string
    {
        return Response::class;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = (string) $path;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPathSuffix(): ?string
    {
        return null;
    }

    /**
     * @param string $parameter
     * @param string $value
     * @param string $separator
     * @return $this
     */
    public function addParameter(string $parameter, string $value, string $separator = '|'): self
    {
        if (isset($this->parameters[$parameter])) {
            if ($value == null) {
                unset($this->parameters[$parameter]);
            } else {
                $this->parameters[$parameter] = $this->parameters[$parameter] . $separator . $value;
            }
        } else $this->parameters[$parameter] = (string) $value;

        return $this;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param string $parameter
     * @param string|null $value
     * @return $this
     */
    public function setParameter(string $parameter, string $value = null): self
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
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param string $parameter
     * @return mixed
     */
    public function getParameter(string $parameter)
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
    public function hasParameter(string $parameter): bool
    {
        return isset($this->parameters[$parameter]);
    }

    /**
     * @param int|Category $category
     * @return $this
     */
    public function addCategoryFilter($category): self
    {
        $ids = [];
        if (is_numeric($category)) {
            $ids[] = $category;
            return $this->addCategoryPathFilter($ids);
        }
        /** @var Category $category */
        $parentIsRoot = in_array(
            (int) $category->getParentId(),
            [
                0,
                1,
                (int) $category->getStore()->getRootCategoryId()
            ],
            true
        );
        if (!$parentIsRoot) {
            // Parent category is added so that category menu is retained on the deepest category level
            $ids[] = (int) $category->getParentId();
        }
        $ids[] = (int) $category->getId();

        return $this->addCategoryPathFilter($ids);
    }

    /**
     * @param array $categoryIds
     * @return $this
     */
    public function addCategoryPathFilter(array $categoryIds): self
    {
        $categoryIds = array_map('intval', $categoryIds);
        $storeId = (int) $this->getStoreId();
        $tweakwiseIdMapper = function (int $categoryId) use ($storeId) {
            return $this->helper->getTweakwiseId($storeId, $categoryId);
        };
        $tweakwiseIds = array_map($tweakwiseIdMapper, $categoryIds);
        $this->setParameter('tn_cid', implode('-', $tweakwiseIds));
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCategoryPathFilter(): ?string
    {
        if (!$categoryPath = $this->getParameter('tn_cid')) {
            return null;
        }

        if (!is_string($categoryPath)) {
            return null;
        }

        $magentoIdMapper = function (int $tweakwiseCategoryId) {
            return $this->helper->getStoreId($tweakwiseCategoryId);
        };

        $categoryPath = array_map($magentoIdMapper, explode('-', $categoryPath));
        return implode('-', $categoryPath);
    }

    /**
     * @return StoreInterface|null
     */
    protected function getStore(): ?StoreInterface
    {
        try {
            return $this->storeManager->getStore();
        } catch (NoSuchEntityException $e) {
            // Chose to not implement a good catch as this will not happen in practice.
            return null;
        }
    }

    /**
     * @return int|null
     */
    protected function getStoreId(): ?int
    {
        $store = $this->getStore();
        if ($store instanceof StoreInterface) {
            return $store->getId();
        }

        return null;
    }
}
