<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Request\Suggestions;

use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Request\SearchRequestInterface;
use Emico\Tweakwise\Model\Client\Request\SearchRequestTrait;
use Emico\Tweakwise\Model\Config;
use Emico\TweakwiseExport\Model\Helper;
use Magento\Store\Model\StoreManager;
use Emico\Tweakwise\Model\Client\Response\Suggestions\ProductSuggestionsResponse;

class ProductSuggestionsRequest extends Request implements SearchRequestInterface
{
    use SearchRequestTrait;

    /**
     * {@inheritDoc}
     */
    protected $path = 'suggestions/products';

    /**
     * ProductSuggestionRequest constructor.
     * @param Helper $helper
     * @param StoreManager $storeManager
     * @param Config $config
     */
    public function __construct(
        Helper $helper,
        StoreManager $storeManager,
        Config $config
    ) {
        parent::__construct($helper, $storeManager);
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getResponseType()
    {
        return ProductSuggestionsResponse::class;
    }
}
