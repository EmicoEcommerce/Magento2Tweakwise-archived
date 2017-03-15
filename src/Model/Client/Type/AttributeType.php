<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Client\Type;

/**
 * @method AttributeType[] getChildren();
 */
class AttributeType extends Type
{
    /**
     * @param AttributeType[]|array[] $children
     * @return $this
     */
    public function setChildren(array $children)
    {
        $children = $this->normalizeArray($children, 'attribute');

        $values = [];
        foreach ($children as $value) {
            if (!$value instanceof AttributeType) {
                $value = new AttributeType($value);
            }

            $values[] = $value;
        }

        $this->data['children'] = $values;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return (string) $this->getDataValue('title');
    }

    /**
     * @return bool
     */
    public function getIsSelected()
    {
        return $this->getDataValue('isselected') == 'true';
    }

    /**
     * @return int
     */
    public function getNumberOfResults()
    {
        return (int) $this->getDataValue('nrofresults');
    }

    /**
     * @return int
     */
    public function getAttributeId()
    {
        return (string) $this->getDataValue('attributeid');
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return (string) $this->getDataValue('url');
    }
}