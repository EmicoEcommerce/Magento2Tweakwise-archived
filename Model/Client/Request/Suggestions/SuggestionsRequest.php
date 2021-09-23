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
use JetBrains\PhpStorm\Pure;
use Magento\Store\Model\StoreManager;
use Emico\Tweakwise\Model\Client\Response\Suggestions\SuggestionsResponse;

class SuggestionsRequest extends Request implements SearchRequestInterface
{
    use SearchRequestTrait;

    /**
     * {@inheritDoc}
     */
    protected string $path = 'suggestions';

    /**
     * SuggestionRequest constructor.
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
    public function getResponseType(): string
    {
        return SuggestionsResponse::class;
    }
}
