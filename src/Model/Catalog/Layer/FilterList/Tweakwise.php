<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\FilterList;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;

class Tweakwise
{
    /**
     * @var NavigationContext
     */
    protected $navigationContext;

    /**
     * Tweakwise constructor.
     *
     * @param NavigationContext $navigationContext
     */
    public function __construct(NavigationContext $navigationContext)
    {
        $this->navigationContext = $navigationContext;
    }

    /**
     * @param Layer $layer
     * @return AbstractFilter[]
     */
    public function getFilters(Layer $layer)
    {
        return [];
    }
}