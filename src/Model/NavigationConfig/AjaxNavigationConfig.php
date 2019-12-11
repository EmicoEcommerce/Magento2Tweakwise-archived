<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
declare(strict_types=1);

namespace Emico\Tweakwise\Model\NavigationConfig;

use Emico\Tweakwise\Model\Config;

class AjaxNavigationConfig implements NavigationConfigInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * AjaxNavigationConfig constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function getJsFilterNavigationConfig(bool $hasAlternateSortOrder = false)
    {
        $config[] = [
            'tweakwiseNavigationSort' => [
                'hasAlternateSortOrder' => $hasAlternateSortOrder
            ]
        ];

        $config[] = [
            'tweakwiseNavigationFilterAjax' => [
                'seoEnabled' => $this->config->isSeoEnabled(),
                'ajaxEndpoint' => 'tweakwise/ajax/navigation',
                'filterSelector' => '#layered-filter-block',
                'productListSelector'
            ],
        ];

        return $config;
    }

    /**
     * @inheritDoc
     */
    public function getJsFormConfig()
    {
        return '';
    }
}
