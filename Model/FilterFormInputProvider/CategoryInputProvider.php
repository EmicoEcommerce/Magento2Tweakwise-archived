<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\FilterFormInputProvider;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Emico\Tweakwise\Model\Config;

class CategoryInputProvider implements FilterFormInputProviderInterface
{
    public const TYPE = 'category';

    /**
     * @var Registry
     */
    protected Registry $registry;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categoryRepository;

    /**
     * @var CategoryInterfaceFactory
     */
    protected CategoryInterfaceFactory $categoryFactory;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $url;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var ToolbarInputProvider
     */
    protected ToolbarInputProvider $toolbarInputProvider;

    /**
     * CategoryParameterProvider constructor.
     * @param UrlInterface $url
     * @param Registry $registry
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryFactory
     * @param ToolbarInputProvider $toolbarInputProvider
     */
    public function __construct(
        UrlInterface $url,
        Registry $registry,
        Config $config,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory,
        ToolbarInputProvider $toolbarInputProvider
    ) {
        $this->url = $url;
        $this->registry = $registry;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->toolbarInputProvider = $toolbarInputProvider;
    }

    /**
     * @inheritDoc
     */
    public function getFilterFormInput(): array
    {
        if (!$this->config->isAjaxFilters()) {
            return [];
        }

        $input = [
            '__tw_ajax_type' => self::TYPE,
            '__tw_original_url' => $this->getOriginalUrl(),
            '__tw_object_id' => $this->getCategoryId()
        ];

        return array_merge($input, $this->toolbarInputProvider->getFilterFormInput());
    }

    /**
     * Public because of plugin options
     *
     * @return string
     */
    public function getOriginalUrl(): string
    {
        return str_replace($this->url->getBaseUrl(), '', $this->getCategory()->getUrl());
    }

    /**
     * @return int|null
     */
    public function getCategoryId(): ?int
    {
        return (int)$this->getCategory()->getId() ?: null;
    }

    /**
     * @return CategoryInterface|Category
     */
    protected function getCategory(): Category|CategoryInterface
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
