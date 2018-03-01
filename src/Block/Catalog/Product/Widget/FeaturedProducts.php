<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Block\Catalog\Product\Widget;

use Emico\Tweakwise\Block\Catalog\Product\ProductList\Featured;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Collection;
use Emico\Tweakwise\Model\Client\Request\Recommendations\FeaturedRequest;
use Magento\Widget\Block\BlockInterface;

/**
 * Class FeaturedProducts
 *
 * @package Emico\Tweakwise\Block\Catalog\Product\Widget
 * @method int getRuleId();
 * @method string getDisplayType();
 * @method bool getCanItemsAddToCart();
 */
class FeaturedProducts extends Featured implements BlockInterface
{
    /**
     * Set default template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magento_Catalog::product/list/items.phtml');
    }

    /**
     * @return bool
     */
    protected function checkRecommendationEnabled()
    {
        return (bool) $this->getRuleId();
    }

    /**
     * @param $request
     */
    protected function configureRequest(FeaturedRequest $request)
    {
        $request->setTemplate($this->getRuleId());
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'new';
    }

    /**
     * @return Collection
     */
    public function getItems()
    {
        return $this->_getProductCollection();
    }
}