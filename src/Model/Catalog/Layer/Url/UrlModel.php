<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Config\Source\QueryFilterType;
use Magento\Framework\Url as MagentoUrl;

class UrlModel extends MagentoUrl
{
    /**
     * @var Config
     */
    private $config;
    
    /**
     * Used config as method injection to prevent overriding the constructor. The constructor changed it's arguments
     * between 2.1 and 2.2 and so it was not possible any longer to support both versions of Magento in one using constructor injection.
     *
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Return query string with filtered params
     *
     * @param bool $escape
     * @return string
     */
    protected function _getQuery($escape = false)
    {
        if ($this->config->getQueryFilterType() === QueryFilterType::TYPE_NONE) {
            return parent::_getQuery($escape);
        }

        $newParams = [];
        foreach ($this->_queryParamsResolver->getQueryParams() as $param => $value) {
            if ($this->shouldFilter($param)) {
                continue;
            }

            $newParams[$param] = $value;
        }
        $this->_queryParamsResolver->setQueryParams($newParams);

        return parent::_getQuery($escape);
    }

    /**
     * @param string $param
     * @return bool
     */
    private function shouldFilter($param)
    {
        $filterType = $this->config->getQueryFilterType();
        if ($filterType === QueryFilterType::TYPE_NONE) {
            return false;
        }

        if ($filterType === QueryFilterType::TYPE_REGEX) {
            return (bool) preg_match('/' . $this->config->getQueryFilterRegex() . '/', $param);
        }

        if ($filterType === QueryFilterType::TYPE_SPECIFIC) {
            return \in_array($param, $this->config->getQueryFilterArguments(), true);
        }

        return true;
    }
}