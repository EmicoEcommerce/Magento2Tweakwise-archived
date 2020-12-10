<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Response;

use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Response;
use Emico\Tweakwise\Model\Client\Type\ItemType;
use Emico\TweakwiseExport\Model\Helper;

/**
 * Class RecommendationsResponse
 *
 * @package Emico\Tweakwise\Model\Client\Response
 */
class RecommendationsResponse extends Response
{
    /**
     * @param array $recommendation
     */
    public function setRecommendation(array $recommendation)
    {
        if (!empty($recommendation) && !isset($recommendation['items'])) {
            // In this case multiple recommendations are given (group code)
            $recommendations = $recommendation;
            foreach ($recommendations as $recommendationEntry) {
                $this->setData($recommendationEntry);
            }

            return;
        }

        $this->setData($recommendation);
    }

    /**
     * @return ItemType[]
     */
    public function getItems(): array
    {
        return $this->getDataValue('items') ?: [];
    }

    /**
     * @param ItemType[]|array[] $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $items = $this->normalizeArray($items, 'item');

        foreach ($items as $value) {
            if (!$value instanceof ItemType) {
                $value = new ItemType($value);
            }

            $this->data['items'][] = $value;
        }

        return $this;
    }

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        $ids = [];
        foreach ($this->getItems() as $item) {
            $ids[] = $this->helper->getStoreId($item->getId());
        }

        return $ids;
    }
}
