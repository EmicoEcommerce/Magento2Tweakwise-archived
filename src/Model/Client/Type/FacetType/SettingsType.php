<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
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
 * @method string getSelectionType();
 * @method int getNumberOfColumns();
 * @method boolean getIsNumberOfResultVisible();
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
}