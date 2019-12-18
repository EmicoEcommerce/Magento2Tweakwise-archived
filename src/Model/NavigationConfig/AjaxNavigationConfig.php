<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
declare(strict_types=1);

namespace Emico\Tweakwise\Model\NavigationConfig;

use Emico\Tweakwise\Model\Config;
use Magento\Framework\UrlInterface;

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
     * AjaxNavigationConfig constructor.
     * @param Config $config
     * @param UrlInterface $url
     */
    public function __construct(
        Config $config,
        UrlInterface $url
    ) {
        $this->config = $config;
        $this->urlHelper = $url;
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
                'ajaxEndpoint' => $this->urlHelper->getUrl('tweakwise/ajax/navigation'),
                'filterSelector' => '#layered-filter-block',
                'productListSelector' => '.products.wrapper',
                'toolbarSelector' => '.toolbar.toolbar-products'
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getJsFormConfig()
    {
        return '';
    }
}
