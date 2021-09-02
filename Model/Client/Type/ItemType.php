<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Type;

/**
 * @method LabelType[] getLabels();
 */
class ItemType extends Type
{
    /**
     * @param LabelType[]|array[] $labels
     * @return $this
     */
    public function setLabels(array $labels): static
    {
        $labels = $this->normalizeArray($labels, 'label');

        $values = [];
        foreach ($labels as $value) {
            if (!$value instanceof LabelType) {
                $value = new LabelType($value);
            }

            $values[] = $value;
        }

        $this->data['labels'] = $values;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getId(): int|string
    {
        return (int) $this->getDataValue('itemno');
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return (string) $this->getDataValue('order');
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return (string) $this->getDataValue('title');
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return (int) $this->getDataValue('price');
    }

    /**
     * @return string
     */
    public function getBrand(): string
    {
        return (string) $this->getDataValue('brand');
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return (string) $this->getDataValue('image');
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return (string) $this->getDataValue('url');
    }
}
