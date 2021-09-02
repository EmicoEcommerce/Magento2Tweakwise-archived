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
    public function getUrlKey(): string
    {
        return (string) $this->getDataValue('urlkey');
    }

    /**
     * This returns the original (magento) attribute this facet is associated with
     * @return string
     */
    public function getAttributename(): string
    {
        return (string) $this->getDataValue('attributename');
    }

    /**
     * @return bool
     */
    public function getIsMultipleSelect(): bool
    {
        return $this->getDataValue('ismultiselect') == 'true';
    }

    /**
     * @return string
     */
    public function getSelectionType(): string
    {
        return (string) $this->getDataValue('selectiontype');
    }

    /**
     * @return string
     */
    public function getIsNumberOfResultVisible(): bool|string
    {
        return $this->getDataValue('isnrofresultsvisible') == 'true';
    }

    /**
     * @return bool
     */
    public function isPrice(): bool
    {
        return $this->getUrlKey() === 'price';
    }

    /**
     * @return bool
     */
    public function getIsCollapsible(): bool
    {
        return $this->getDataValue('iscollapsible') == 'true';
    }

    /**
     * @return bool
     */
    public function getIsCollapsed(): bool
    {
        return $this->getDataValue('iscollapsed') == 'true';
    }

    /**
     * @return bool
     */
    public function getIsInfoVisible(): bool
    {
        return $this->getDataValue('isinfovisible') == 'true';
    }

    /**
     * @return int
     */
    public function getNumberOfColumns(): int
    {
        return (int) $this->getDataValue('nrofcolumns');
    }

    /**
     * @return int
     */
    public function getNumberOfShownAttributes(): int
    {
        return (int) $this->getDataValue('nrofshownattributes');
    }

    /**
     * @return string|null
     */
    public function getInfoText(): ?string
    {
        $infoText = $this->getDataValue('infotext');
        if (!empty($infoText)) {
            return (string) $infoText;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getExpandText(): string
    {
        return (string) $this->getDataValue('expandtext');
    }

    /**
     * @return string
     */
    public function getCollapseText(): string
    {
        return (string) $this->getDataValue('collapsetext');
    }

    /**
     * @return bool
     */
    public function getIsVisible(): bool
    {
        return $this->getDataValue('isvisible') == 'true';
    }

    /**
     * @return int
     */
    public function getMultiSelectLogic(): int
    {
        return (int) $this->getDataValue('multiselectlogic');
    }

    /**
     * @return null|string
     */
    public function getPrefix(): ?string
    {
        $prefix = $this->getDataValue('prefix');
        if (!empty($prefix)) {
            return (string) $prefix;
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getPostfix(): ?string
    {
        $postfix = $this->getDataValue('postfix');
        if (!empty($postfix)) {
            return (string) $postfix;
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getCssClass(): ?string
    {
        $cssClass = $this->getDataValue('cssclass');
        if (!empty($cssClass)) {
            return (string) $cssClass;
        }
        return null;
    }
}
