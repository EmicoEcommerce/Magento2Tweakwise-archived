<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\Navigation\State;

use Closure;
use Emico\Tweakwise\Model\Catalog\Layer\Url;
use Emico\Tweakwise\Model\Config;
use Magento\LayeredNavigation\Block\Navigation\State;

class Plugin
{
    /**
     * @var Url
     */
    private $url;

    /**
     * Plugin constructor.
     *
     * @param Config $config
     * @param Url $url
     */
    public function __construct(Config $config, Url $url)
    {
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * @param State $subject
     * @param Closure $proceed
     * @return string
     */
    public function aroundGetClearUrl(State $subject, Closure $proceed)
    {
        if (!$this->config->isLayeredEnabled()) {
            return $proceed();
        }

        return $this->url->getClearUrl($subject->getActiveFilters());
    }
}
