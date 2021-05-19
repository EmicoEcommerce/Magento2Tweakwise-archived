<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */
namespace Emico\Tweakwise\Model\Autocomplete\DataProvider;

use Emico\Tweakwise\Model\Autocomplete\DataProviderHelper;
use Emico\Tweakwise\Model\Autocomplete\DataProviderInterface;
use Emico\Tweakwise\Model\Client;
use Emico\Tweakwise\Model\Client\Request\Suggestions\ProductSuggestionsRequest;
use Emico\Tweakwise\Model\Client\RequestFactory;
use Emico\Tweakwise\Model\Client\Response\AutocompleteProductResponseInterface;
use Emico\Tweakwise\Model\Client\Response\Suggestions\SuggestionsResponse;
use Emico\Tweakwise\Model\Config;
use function GuzzleHttp\Promise\unwrap;
use Magento\Framework\Exception\LocalizedException;
use Magento\Search\Model\Autocomplete\ItemInterface;

class SuggestionDataProvider implements DataProviderInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DataProviderHelper
     */
    protected $dataProviderHelper;

    /**
     * @var SuggestionGroupItemFactory
     */
    protected $suggestionGroupItemFactory;

    /**
     * @var RequestFactory
     */
    protected $productSuggestionRequestFactory;

    /**
     * @var RequestFactory
     */
    protected $suggestionRequestFactory;

    /**
     * @var Client
     */
    protected $client;

    /**
     * AutocompleteDataProvider constructor.
     * @param Config $config
     * @param DataProviderHelper $dataProviderHelper
     * @param SuggestionGroupItemFactory $suggestionGroupItemFactory
     * @param RequestFactory $productSuggestionRequestFactory
     * @param RequestFactory $suggestionRequestFactory
     * @param Client $client
     */
    public function __construct(
        Config $config,
        DataProviderHelper $dataProviderHelper,
        SuggestionGroupItemFactory $suggestionGroupItemFactory,
        RequestFactory $productSuggestionRequestFactory,
        RequestFactory $suggestionRequestFactory,
        Client $client
    ) {
        $this->config = $config;
        $this->dataProviderHelper = $dataProviderHelper;
        $this->suggestionGroupItemFactory = $suggestionGroupItemFactory;
        $this->productSuggestionRequestFactory = $productSuggestionRequestFactory;
        $this->suggestionRequestFactory = $suggestionRequestFactory;
        $this->client = $client;
    }

    /**
     * @return bool
     */
    public function isSupported(): bool
    {
        return $this->config->isSuggestionsAutocomplete();
    }

    /**
     * @return ItemInterface[]
     * @throws LocalizedException
     * @throws \Throwable
     */
    public function getItems()
    {
        $query = $this->dataProviderHelper->getQuery();
        $category = $this->dataProviderHelper->getCategory();
        $promises = [];

        $suggestionsRequest = $this->suggestionRequestFactory->create();
        $suggestionsRequest->setSearch($query);
        $suggestionsRequest->addCategoryFilter($category);
        $promises['suggestions'] = $this->client->request(
            $suggestionsRequest,
            true
        );

        /** @var ProductSuggestionsRequest $productSuggestionRequest */
        $productSuggestionsRequest = $this->productSuggestionRequestFactory->create();
        $productSuggestionsRequest->setSearch($query);
        $productSuggestionsRequest->addCategoryFilter($category);
        $promises['product_suggestions'] = $this->client->request(
            $productSuggestionsRequest,
            true
        );

        if (empty($promises)) {
            return [];
        }

        $results = [];
        $responses = unwrap($promises);
        foreach ($responses as $key => $response) {
            if ($response instanceof AutocompleteProductResponseInterface) {
                $results[] = $this->dataProviderHelper->getProductItems($response);
            }
            if ($response instanceof SuggestionsResponse) {
                $results[] = $this->getSuggestionGroups($response);
            }
        }

        return (!empty($results)) ? array_merge(...$results) : [];
    }

    /**
     * @param SuggestionsResponse $response
     * @return ItemInterface[]
     */
    protected function getSuggestionGroups(SuggestionsResponse $response)
    {
        $results = [];
        $groups = $response->getGroups() ?: [];
        foreach ($groups as $suggestionGroup) {
            $results[] = $this->suggestionGroupItemFactory->create(['group' => $suggestionGroup]);
        }

        return $results;
    }
}
