<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
declare(strict_types=1);

namespace Emico\Tweakwise\Model\NavigationConfig;

use Emico\Tweakwise\Model\Config;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class NavigationConfig
 * This class provides configuration for the various data-mage-init statements in phtml files.
 * It will be used by
 * @see \Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\DefaultRenderer
 * @see \Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SwatchRenderer
 * @see \Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer
 * @package Emico\Tweakwise\Model
 */
class NavigationConfig
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * NavigationConfig constructor.
     * @param Config $config
     * @param Json $jsonSerializer
     */
    public function __construct(
        Config $config,
        Json $jsonSerializer
    ) {
        $this->config = $config;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param bool $hasAlternateSortOrder
     * @return bool|string
     */
    public function getJsFilterNavigationConfig(bool $hasAlternateSortOrder = false)
    {
        $config[] = [
            'tweakwiseNavigationSort' => [
                'hasAlternateSortOrder' => $hasAlternateSortOrder
            ]
        ];
        if (!$this->config->getUseFormFilters()) {
            $config[] = [
                'tweakwiseNavigationFilter' => [
                    'seoEnabled' => $this->config->isSeoEnabled()
                ],
            ];
        }

        return $this->jsonSerializer->serialize(array_merge(...$config));
    }

    /**
     * @return string
     */
    public function getJsUseFormFilters()
    {
        return $this->jsonSerializer->serialize($this->config->getUseFormFilters());
    }

    /**
     * @return string
     */
    public function getJsFormConfig()
    {
        if (!$this->config->getUseFormFilters()) {
            return '';
        }

        $jsFormConfig = [
            'tweakwiseNavigationForm' => []
        ];

        return $this->jsonSerializer->serialize($jsFormConfig);
    }
}
