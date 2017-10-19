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
 * @method string getTitle();
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

    /**
     * @return bool
     */
    public function getIsCollapsible()
    {
        return $this->getDataValue('iscollapsible') == 'true';
    }

    /**
     * @return bool
     */
    public function getIsCollapsed()
    {
        return $this->getDataValue('iscollapsed') == 'true';
    }

    /**
     * @return bool
     */
    public function getIsInfoVisible()
    {
        return $this->getDataValue('isinfovisible') == 'true';
    }

    /**
     * @return int
     */
    public function getNumberOfColumns()
    {
        return (int) $this->getDataValue('nrofcolumns');
    }

    /**
     * @return int
     */
    public function getNumberOfShownAttributes()
    {
        return (int) $this->getDataValue('nrofshownattributes');
    }

    /**
     * @return string|null
     */
    public function getInfoText()
    {
        $infoText = (string) $this->getDataValue('infotext');
        if ($infoText) {
            return $infoText;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getExpandText()
    {
        return (string) $this->getDataValue('expandtext');
    }

    /**
     * @return string
     */
    public function getCollapseText()
    {
        return (string) $this->getDataValue('collapsetext');
    }

    /**
     * @return bool
     */
    public function getIsVisible()
    {
        return $this->getDataValue('isvisible') == 'true';
    }

    /**
     * @return int
     */
    public function getMultiSelectLogic()
    {
        return (int) $this->getDataValue('multiselectlogic');
    }
}