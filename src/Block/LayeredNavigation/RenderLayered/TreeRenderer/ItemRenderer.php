<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\TreeRenderer;

use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\TreeRenderer;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;

class ItemRenderer extends TreeRenderer
{
    /**
     * {@inheritDoc}
     */
    protected $_template = 'Emico_Tweakwise::product/layered/tree/item.phtml';

    /**
     * @var Item
     */
    protected $item;

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function setItem(Item $item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->item->hasChildren();
    }

    /**
     * @return Item[]
     */
    public function getChildren()
    {
        return $this->item->getChildren();
    }
}