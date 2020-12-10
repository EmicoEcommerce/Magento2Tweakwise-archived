<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\Seo\Robots;

use Magento\Framework\View\Page\Config as PageConfig;
use Emico\Tweakwise\Model\Seo\FilterHelper;
use Emico\Tweakwise\Model\Config as TweakwiseConfig;

class Plugin
{
    /**
     * @var FilterHelper
     */
    protected $filterHelper;

    /**
     * @var TweakwiseConfig
     */
    protected $tweakwiseConfig;

    /**
     * Plugin constructor.
     * @param FilterHelper $filterHelper
     */
    public function __construct(FilterHelper $filterHelper, TweakwiseConfig $tweakwiseConfig)
    {
        $this->filterHelper = $filterHelper;
        $this->tweakwiseConfig = $tweakwiseConfig;
    }

    /**
     * @param PageConfig $config
     * @param $result
     * @return string
     */
    public function afterGetRobots(PageConfig $config, $result)
    {
        if (!$this->tweakwiseConfig->isSeoEnabled()) {
            return $result;
        }

        if ($this->isAlreadyNoIndex($result)) {
            return $result;
        }

        if (!$this->shouldApplyNoindex()) {
            return $result;
        }

        return $this->getNewRobots($result);
    }

    /**
     * @param string $result
     */
    protected function isAlreadyNoIndex(string $result): bool
    {
        return stripos(strtolower($result), 'noindex') !== false;
    }

    /**
     * @param string $oldRobots
     */
    protected function getNewRobots(string $oldRobots): string
    {
        $follow = explode(',', $oldRobots);
        $follow = end($follow);
        return sprintf('NOINDEX,%s', strtoupper($follow));
    }

    /**
     * @return bool
     */
    protected function shouldApplyNoindex(): bool
    {
        return !$this->filterHelper->shouldPageBeIndexable();
    }
}
