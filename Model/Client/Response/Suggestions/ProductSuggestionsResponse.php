<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Response\Suggestions;

use Emico\Tweakwise\Model\Client\Response;
use Emico\Tweakwise\Model\Client\Response\AutocompleteProductResponseInterface;
use Emico\Tweakwise\Model\Client\Type\ItemType;

/**
 * Class ProductSuggestionsResponse
 * @package Emico\Tweakwise\Model\Client\Response\Suggestions
 *
 * @method ItemType[] getItems();
 */
class ProductSuggestionsResponse extends Response implements AutocompleteProductResponseInterface
{
    /**
     * @param ItemType[]|array[] $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $items = $this->normalizeArray($items, 'item');

        $values = [];
        foreach ($items as $value) {
            if (!$value instanceof ItemType) {
                $value = new ItemType($value);
            }

            $values[] = $value;
        }

        $this->data['items'] = $values;
        return $this;
    }

    /**
     * @return int[]
     */
    public function getProductIds()
    {
        $ids = [];
        foreach ($this->getItems() as $item) {
            $ids[] = $this->helper->getStoreId($item->getId());
        }
        return $ids;
    }

    /**
     * @return array
     */
    public function getProductData()
    {
        $result = [];
        foreach ($this->getItems() as $item) {
            $result[] = [
                'id' => $this->helper->getStoreId($item->getId()),
                'tweakwise_price' => (float) $item->getPrice(),
            ];
        }
        return $result;
    }
}
