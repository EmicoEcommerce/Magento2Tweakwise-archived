<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\Navigation\State;

use Closure;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Catalog\Layer\Url;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Emico\Tweakwise\Model\Config;
use Magento\LayeredNavigation\Block\Navigation\State;

class Plugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var CurrentContext
     */
    private $currentContext;

    /**
     * Plugin constructor.
     *
     * @param Config $config
     * @param Url $url
     * @param CurrentContext $currentContext
     */
    public function __construct(
        Config $config,
        Url $url,
        CurrentContext $currentContext
    ) {
        $this->config = $config;
        $this->url = $url;
        $this->currentContext = $currentContext;
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

    /**
     * @param State $subject
     * @param string|null $template
     * @return string|null
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterGetTemplate(State $subject, string $template = null)
    {
        if ($this->config->getUseDefaultLinkRenderer()) {
            return $template;
        }

        $searchEnabled = $this->config->isSearchEnabled();
        $navigationEnabled = $this->config->isLayeredEnabled();

        $isSearch = $this->currentContext->getRequest() instanceof ProductSearchRequest;
        $isNavigation = !$isSearch;

        if ($isSearch && $searchEnabled) {
            return 'Emico_Tweakwise::layer/state.phtml';
        }

        if ($isNavigation && $navigationEnabled) {
            return 'Emico_Tweakwise::layer/state.phtml';
        }

        return $template;
    }
}
