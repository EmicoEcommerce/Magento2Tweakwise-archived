<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Magento\Framework\View\Element\Template;

abstract class AbstractRenderer extends Template
{
    /**
     * {@inheritDoc}
     */
    protected $_template = 'Emico_Tweakwise::product/layered/default.phtml';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @param Filter $filter
     */
    public function setFilter(Filter $filter)
    {
        $this->filter = $filter;
    }
}