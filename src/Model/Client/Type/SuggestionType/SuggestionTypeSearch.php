<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\QueryParameterStrategy;
use Emico\TweakwiseExport\Model\Helper;
use Magento\Framework\UrlInterface;

/**
 * Class SuggestionTypeSearch
 * @package Emico\Tweakwise\Model\Client\Type\SuggestionType
 */
class SuggestionTypeSearch extends SuggestionTypeAbstract
{
    public const TYPE = 'SearchPhrase';

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->getSearchUrl();
    }
}
