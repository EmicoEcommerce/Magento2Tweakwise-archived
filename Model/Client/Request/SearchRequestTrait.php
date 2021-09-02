<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Request;

use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;

trait SearchRequestTrait
{
    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @param string $parameter
     * @param string|null $value
     * @return mixed
     */
    abstract public function setParameter(string $parameter, string $value = null): mixed;

    /**
     * @param string $parameter
     * @return mixed|null
     */
    abstract public function getParameter(string $parameter): mixed;

    /**
     * @return StoreInterface|null
     */
    abstract protected function getStore(): ?StoreInterface;

    /**
     * @param $category int|Category
     */
    abstract protected function addCategoryFilter(Category|int $category);

    /**
     * @param string $query
     */
    public function setSearch(string $query): void
    {
        $this->setParameter('tn_q', $query);
        $this->setDefaultCategory();
        $this->setSearchLanguage();
    }

    /**
     *
     */
    protected function setDefaultCategory(): void
    {
        if ($this->getParameter('tn_cid') === null) {
            $rootCategoryId = $this->getStoreRootCategoryId() ?: 2;
            $this->addCategoryFilter($rootCategoryId);
        }
    }

    /**
     * @return int|null
     */
    protected function getStoreRootCategoryId(): ?int
    {
        $store = $this->getStore();
        if ($store instanceof Store) {
            /** @noinspection PhpUnhandledExceptionInspection */
            return (int) $store->getRootCategoryId();
        }

        return null;
    }

    /**
     *
     */
    protected function setSearchLanguage(): void
    {
        $language = $this->config->getSearchLanguage();
        if (!$language) {
            return;
        }

        $this->setParameter('tn_lang', $language);
    }
}
