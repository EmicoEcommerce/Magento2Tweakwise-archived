<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
declare(strict_types=1);

namespace Emico\Tweakwise\Model\NavigationConfig;

use Emico\Tweakwise\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class AjaxNavigationConfig implements NavigationConfigInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var UrlInterface
     */
    protected $urlHelper;

    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * AjaxNavigationConfig constructor.
     * @param Config $config
     * @param UrlInterface $url
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        UrlInterface $url,
        Registry $registry,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->urlHelper = $url;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function getJsFilterNavigationConfig(bool $hasAlternateSortOrder = false)
    {
        return [
            'tweakwiseNavigationSort' => [
                'hasAlternateSortOrder' => $hasAlternateSortOrder
            ],
            'tweakwiseNavigationFilterAjax' => [
                'seoEnabled' => $this->config->isSeoEnabled(),
                'categoryId' => $this->getCategoryId(),
                'ajaxEndpoint' => $this->urlHelper->getUrl('tweakwise/ajax/navigation'),
                'filterSelector' => '#layered-filter-block',
                'productListSelector' => '.products.wrapper',
                'toolbarSelector' => '.toolbar.toolbar-products'
            ],
        ];
    }

    /**
     * @return mixed
     */
    protected function getCategoryId()
    {
        if ($currentCategory = $this->registry->registry('current_category')) {
            return (int)$currentCategory->getId();
        }

        try {
            return (int)$this->storeManager->getStore()->getRootCategoryId();
        } catch (NoSuchEntityException $e) {
            return 2;
        }
    }

    /**
     * @inheritDoc
     */
    public function getJsFormConfig()
    {
        return '';
    }
}
