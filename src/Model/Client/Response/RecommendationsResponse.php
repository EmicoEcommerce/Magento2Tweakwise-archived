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
     * RecommendationsResponse constructor.
     * @param Helper $helper
     * @param Request $request
     * @param array|null $data
     */
    public function __construct(
        Helper $helper,
        Request $request,
        array $data = null
    ) {
        parent::__construct($helper, $request, $data);
    }

    /**
     * @param array $recommendation
     */
    public function setRecommendation(array $recommendation)
    {
        if (!empty($recommendation) && !isset($recommendation['items'])) {
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
        $data = $this->getDataValue('items');
        if (!$data) {
            return [];
        }

        return $data;
    }

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
    public function getProductIds(): array
    {
        $ids = [];
        foreach ($this->getItems() as $item) {
            $ids[] = $this->helper->getStoreId($item->getId());
        }

        return $ids;
    }
}
