<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Autocomplete;

use Emico\Tweakwise\Model\Autocomplete\DataProviderInterface as TweakwiseDataProviderInterface;
use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Magento\Search\Model\Autocomplete\ItemInterface;

class DataProvider implements DataProviderInterface
{
    /**
     * @var TweakwiseDataProviderInterface[]
     */
    protected $dataProviders;

    /**
     * DataProvider constructor.
     *
     * @param TweakwiseDataProviderInterface[] $dataProviders
     */
    public function __construct(array $dataProviders)
    {
        $this->dataProviders = $dataProviders;
    }

    /**
     * @return ItemInterface[]
     */
    public function getItems()
    {
        $items = [];
        foreach ($this->dataProviders as $dataProvider) {
            if ($dataProvider->isSupported()) {
                $items[] = $dataProvider->getItems();
            }
        }

        return !empty($items) ? array_merge([], ...$items) : [];
    }
}
