<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\FilterFormParameterProvider;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Emico\Tweakwise\Model\Config;

class CategoryParameterProvider implements FilterFormParameterProviderInterface
{
    public const TYPE = 'category';

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CategoryInterfaceFactory
     */
    protected $categoryFactory;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var Config
     */
    protected $config;

    /**
     * CategoryParameterProvider constructor.
     * @param UrlInterface $url
     * @param Registry $registry
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryFactory
     */
    public function __construct(
        UrlInterface $url,
        Registry $registry,
        Config $config,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory
    ) {
        $this->url = $url;
        $this->registry = $registry;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @inheritDoc
     */
    public function getFilterFormParameters(): array
    {
        if (!$this->config->isAjaxFilters()) {
            return [];
        }

        return [
            '__tw_ajax_type' => self::TYPE,
            '__tw_original_url' => $this->getOriginalUrl(),
            '__tw_object_id' => $this->getCategoryId()
        ];
    }

    /**
     * Public because of plugin options
     *
     * @return string
     */
    public function getOriginalUrl()
    {
        return str_replace($this->url->getBaseUrl(), '', $this->getCategory()->getUrl());
    }

    /**
     * @return int|null
     */
    public function getCategoryId()
    {
        return (int)$this->getCategory()->getId() ?: null;
    }

    /**
     * @return CategoryInterface|Category
     */
    protected function getCategory()
    {
        if ($currentCategory = $this->registry->registry('current_category')) {
            return $currentCategory;
        }

        try {
            $rootCategory = $this->storeManager->getStore()->getRootCategoryId();
        } catch (NoSuchEntityException $exception) {
            $rootCategory = 2;
        }

        try {
            return $this->categoryRepository->get($rootCategory);
        } catch (NoSuchEntityException $exception) {
            return $this->categoryFactory->create();
        }
    }
}
