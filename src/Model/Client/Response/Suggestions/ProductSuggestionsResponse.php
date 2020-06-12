<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Response\Suggestions;

use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Response;
use Emico\Tweakwise\Model\Client\Type\ItemType;
use Emico\TweakwiseExport\Model\Helper;

/**
 * Class ProductSuggestionsResponse
 * @package Emico\Tweakwise\Model\Client\Response\Suggestions
 *
 * @method ItemType[] getItems();
 */
class ProductSuggestionsResponse extends Response
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
}
