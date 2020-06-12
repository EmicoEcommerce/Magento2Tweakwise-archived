<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Autocomplete\DataProvider;

use Emico\Tweakwise\Model\Client\Type\SuggestionTypeAutocomplete;
use Magento\Search\Model\Autocomplete\ItemInterface;

class SuggestionItem implements ItemInterface
{
    /**
     * @var SuggestionTypeAutocomplete
     */
    protected $suggestion;

    /**
     * SuggestionItem constructor.
     *
     * @param SuggestionTypeAutocomplete $suggestion
     */
    public function __construct(SuggestionTypeAutocomplete $suggestion)
    {
        $this->suggestion = $suggestion;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->suggestion->getTitle();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'title' => $this->getTitle(),
            'type' => 'suggestion',
        ];
    }
}
