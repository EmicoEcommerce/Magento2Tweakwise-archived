<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Config\Source\QueryFilterType;
use Magento\Framework\Url as MagentoUrl;

class UrlModel extends MagentoUrl
{
    /**
     * @return Config
     */
    protected function getConfig(): Config
    {
        return $this->getData('tw_config');
    }

    /**
     * Return query string with filtered params
     *
     * @param bool $escape
     * @return string
     */
    protected function _getQuery($escape = false)
    {
        if ($this->getConfig()->getQueryFilterType() === QueryFilterType::TYPE_NONE) {
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
        $filterType = $this->getConfig()->getQueryFilterType();
        if ($filterType === QueryFilterType::TYPE_NONE) {
            return false;
        }

        if ($filterType === QueryFilterType::TYPE_REGEX) {
            return (bool) preg_match('/' . $this->getConfig()->getQueryFilterRegex() . '/', $param);
        }

        if ($filterType === QueryFilterType::TYPE_SPECIFIC) {
            return \in_array($param, $this->getConfig()->getQueryFilterArguments(), true);
        }

        return true;
    }
}