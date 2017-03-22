<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Navigation\FilterRenderer;

use Closure;
use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\AbstractRenderer;
use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\CheckboxRenderer;
use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\ColorRenderer;
use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\LinkRenderer;
use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer;
use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\TreeRenderer;
use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\Tweakwise\Model\Config;
use Emico\TweakwiseExport\Model\Logger;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\LayeredNavigation\Block\Navigation\FilterRenderer;

class Plugin
{
    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Logger
     */
    protected $log;

    /**
     * Filter renderer block types
     *
     * @var string[]
     */
    protected $blockTypes = [
        SettingsType::SELECTION_TYPE_TREE => TreeRenderer::class,
        SettingsType::SELECTION_TYPE_SLIDER => SliderRenderer::class,
        SettingsType::SELECTION_TYPE_LINK => LinkRenderer::class,
        SettingsType::SELECTION_TYPE_CHECKBOX => CheckboxRenderer::class,
        SettingsType::SELECTION_TYPE_COLOR => ColorRenderer::class,
    ];

    /**
     * @var string[]
     */
    protected $defaultAllowedRenderTypes = [
        SettingsType::SELECTION_TYPE_LINK,
        SettingsType::SELECTION_TYPE_CHECKBOX,
        SettingsType::SELECTION_TYPE_COLOR,
    ];

    /**
     * @param Logger $log
     * @param LayoutInterface $layout
     * @param Config $config
     */
    public function __construct(Logger $log, LayoutInterface $layout, Config $config)
    {
        $this->layout = $layout;
        $this->config = $config;
        $this->log = $log;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param FilterRenderer $subject
     * @param Closure $proceed
     * @param FilterInterface $filter
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundRender(FilterRenderer $subject, Closure $proceed, FilterInterface $filter)
    {
        if (!$filter instanceof Filter) {
            return $proceed($filter);
        }

        if (!$this->config->isLayeredEnabled()) {
            return $proceed($filter);
        }

        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();
        $renderType = $settings->getSelectionType();
        if ($this->config->getUseDefaultLinkRenderer() && in_array($renderType, $this->defaultAllowedRenderTypes)) {
            return $proceed($filter);
        }

        $blockType = isset($this->blockTypes[$renderType]) ? $this->blockTypes[$renderType] : CheckboxRenderer::class;
        $block = $this->layout->createBlock($blockType);

        if (!$block instanceof AbstractRenderer) {
            $this->log->error(sprintf('Invalid renderer block type %s not instanceof %s', $blockType, AbstractRenderer::class));
            return $proceed($filter);
        }

        $block->setFilter($filter);
        return $block->toHtml();
    }
}