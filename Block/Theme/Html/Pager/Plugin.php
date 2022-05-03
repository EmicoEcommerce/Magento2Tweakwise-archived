<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\Theme\Html\Pager;

use Closure;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlModel;
use Emico\Tweakwise\Model\Config;
use Magento\Theme\Block\Html\Pager;

class Plugin
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var UrlModel
     */
    protected $url;

    /**
     * Plugin constructor.
     *
     * @param Config $config
     * @param UrlModel $url
     */
    public function __construct(Config $config, UrlModel $url)
    {
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * @param Pager $subject
     * @param Closure $proceed
     * @param string $route
     * @param array $params
     * @return string
     */
    public function aroundGetUrl(Pager $subject, Closure $proceed, string $route = '', array $params = []): string
    {
        if (!$this->config->isLayeredEnabled()) {
            return $proceed($route, $params);
        }

        if ($subject->getNameInLayout() !== 'product_list_toolbar_pager') {
            return $proceed($route, $params);
        }

        $uri = $this->url->getUrl($route, $params);

        if (mb_stripos($uri, 'tweakwise/ajax/navigation') !== false) {
            $params['_direct'] = $subject->getRequest()->getParam('__tw_original_url');

            if (!empty($params['_direct'])) {
                $uri = $this->url->getUrl($route, $params);
            }
        }

        return $uri;
    }
}
