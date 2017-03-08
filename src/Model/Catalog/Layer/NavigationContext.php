<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer;


use Emico\Tweakwise\Model\Client;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\RequestFactory;
use Emico\Tweakwise\Model\Client\Response\ProductNavigationResponse;

/**
 * Class to keep navigation context for page request. This ensures a single request for navigation data facet's and products.
 */
class NavigationContext
{
    /**
     * @var ProductNavigationRequest
     */
    protected $request;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ProductNavigationResponse
     */
    protected $response;

    /**
     * NavigationContext constructor.
     *
     * @param RequestFactory $requestFactory
     * @param Client $client
     */
    public function __construct(RequestFactory $requestFactory, Client $client)
    {
        $this->requestFactory = $requestFactory;
        $this->client = $client;
    }

    /**
     * @return ProductNavigationRequest
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = $this->requestFactory->create();
        }
        return $this->request;
    }

    /**
     * @return ProductNavigationResponse
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = $this->client->request($this->getRequest());
        }

        return $this->response;
    }
}