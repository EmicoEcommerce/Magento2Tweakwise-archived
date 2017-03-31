<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Type\FacetType;

use Emico\Tweakwise\Model\Client\Type\Type;

/**
 * @method int getFacetId();
 * @method boolean getIsVisible();
 * @method string getTitle();
 * @method boolean getIsCollapsible();
 * @method boolean getIsCollapsed();
 * @method int getNumberOfShownAttributes();
 * @method string getExpandText();
 * @method string getCollapseText();
 * @method int getMultiSelectLogic();
 * @method int getNumberOfColumns();
 * @method boolean getIsInfoVisible();
 * @method string getInfoText();
 * @method int getSource();
 * @method int getPrefix();
 * @method int getPostfix();
 */
class SettingsType extends Type
{
    /**
     * Source type from attributes
     */
    const SOURCE_CATEGORY = 'CATEGORY';
    const SOURCE_FEED = 'FEED';

    /**
     * Tweakwise selection types
     */
    const SELECTION_TYPE_LINK = 'link';
    const SELECTION_TYPE_SLIDER = 'slider';
    const SELECTION_TYPE_CHECKBOX = 'checkbox';
    const SELECTION_TYPE_COLOR = 'color';
    const SELECTION_TYPE_TREE = 'tree';

    /**
     * @return string
     */
    public function getUrlKey()
    {
        return (string) $this->getDataValue('urlkey');
    }

    /**
     * @return bool
     */
    public function getIsMultipleSelect()
    {
        return $this->getDataValue('ismultiselect') == 'true';
    }

    /**
     * @return string
     */
    public function getSelectionType()
    {
        return (string) $this->getDataValue('selectiontype');
    }

    /**
     * @return string
     */
    public function getIsNumberOfResultVisible()
    {
        return $this->getDataValue('isnrofresultsvisible') == 'true';
    }

    /**
     * @return bool
     */
    public function isPrice()
    {
        return $this->getUrlKey() == 'price';
    }
}