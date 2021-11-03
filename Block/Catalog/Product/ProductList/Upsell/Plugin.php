<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\Catalog\Product\ProductList\Upsell;

use Closure;
use Emico\Tweakwise\Block\Catalog\Product\ProductList\AbstractRecommendationPlugin;
use Emico\Tweakwise\Exception\ApiException;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Collection;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Block\Product\ProductList\Upsell;

class Plugin extends AbstractRecommendationPlugin
{
    /**
     * @return string
     */
    protected function getType()
    {
        return Config::RECOMMENDATION_TYPE_UPSELL;
    }

    /**
     * @param Upsell $subject
     * @param Closure $proceed
     * @return Collection
     */
    public function aroundGetItemCollection(Upsell $subject, Closure $proceed)
    {
        if (!$this->config->isRecommendationsEnabled(Config::RECOMMENDATION_TYPE_UPSELL)) {
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

    /**
     * @param Upsell $subject
     * @param Closure $proceed
     * @return int
     */
    public function aroundGetItemLimit(Upsell $subject, Closure $proceed, $type = '')
    {
        if (!$this->config->isRecommendationsEnabled(Config::RECOMMENDATION_TYPE_UPSELL)) {
            return $proceed($type);
        }

        try {
            return $this->getCollection()->count();
        } catch (ApiException $e) {
            return $proceed($type);
        }
    }
}
