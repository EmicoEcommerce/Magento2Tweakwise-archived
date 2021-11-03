<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\Catalog\Product\ProductList\Related;

use Closure;
use Emico\Tweakwise\Block\Catalog\Product\ProductList\AbstractRecommendationPlugin;
use Emico\Tweakwise\Exception\ApiException;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Block\Product\ProductList\Related;

class Plugin extends AbstractRecommendationPlugin
{
    /**
     * @return string
     */
    protected function getType()
    {
        return Config::RECOMMENDATION_TYPE_CROSSSELL;
    }

    /**
     * @param Related $subject
     * @param Closure $proceed
     * @return array
     */
    public function aroundGetItems(Related $subject, Closure $proceed)
    {
        if (!$this->config->isRecommendationsEnabled(Config::RECOMMENDATION_TYPE_CROSSSELL)) {
            return $proceed();
        }
        if (!$this->templateFinder->forProduct($subject->getProduct(), $this->getType())) {
            return $proceed();
        }

        try {
            return $this->getCollection();
        } catch (ApiException $e) {
            return $proceed();
        }
    }
}