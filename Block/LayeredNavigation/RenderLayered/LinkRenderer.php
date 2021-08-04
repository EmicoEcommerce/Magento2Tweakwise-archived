<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered;

use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\LinkRenderer\ItemRenderer;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;

/**
 * Class LinkRenderer
 * @package Emico\Tweakwise\Block\LayeredNavigation\RenderLayered
 */
class LinkRenderer extends DefaultRenderer
{
    /**
     * {@inheritDoc}
     */
    protected $_template = 'Emico_Tweakwise::product/layered/link.phtml';

    /**
     * @param Item $item
     * @return string
     */
    public function renderLinkItem(Item $item)
    {
        /** @var ItemRenderer $block */
        $block = $this->getLayout()->createBlock(ItemRenderer::class);
        $block->setFilter($this->filter);
        $block->setItem($item);
        return $block->toHtml();
    }
}
