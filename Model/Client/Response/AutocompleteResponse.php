<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Response;

use Emico\Tweakwise\Model\Client\Response;
use Emico\Tweakwise\Model\Client\Type\InstantSearchType;
use Emico\Tweakwise\Model\Client\Type\ItemType;
use Emico\Tweakwise\Model\Client\Type\SuggestionTypeAutocomplete;

/**
 * Class ProductNavigationResponse
 *
 * @method ItemType[] getItems();
 * @method SuggestionTypeAutocomplete[] getSuggestions();
 * @method InstantSearchType getInstantSearch();
 */
class AutocompleteResponse extends Response implements AutocompleteProductResponseInterface
{
    /**
     * @param SuggestionTypeAutocomplete[]|array[] $suggestions
     * @return $this
     */
    public function setSuggestions(array $suggestions)
    {
        $suggestions = $this->normalizeArray($suggestions, 'suggestion');

        $values = [];
        foreach ($suggestions as $value) {
            if (!$value instanceof SuggestionTypeAutocomplete) {
                $value = new SuggestionTypeAutocomplete($value);
            }

            $values[] = $value;
        }

        $this->data['suggestions'] = $values;
        return $this;
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
     * @param InstantSearchType|array $instantSearch
     * @return $this
     */
    public function setInstantSearch($instantSearch)
    {
        if (!$instantSearch instanceof InstantSearchType) {
            $instantSearch = new InstantSearchType($instantSearch);
        }

        $this->data['instant_search'] = $instantSearch;
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
