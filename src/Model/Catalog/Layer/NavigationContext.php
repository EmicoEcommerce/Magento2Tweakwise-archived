<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer;


use Emico\Tweakwise\Model\Catalog\Layer\FilterList\Tweakwise as TweakwiseFilterList;
use Emico\Tweakwise\Model\Client;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\RequestFactory;
use Emico\Tweakwise\Model\Client\Response\ProductNavigationResponse;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Magento\Catalog\Model\ResourceModel\Attribute;

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
     * @var Url
     */
    protected $url;

    /**
     * @var FilterableAttributeListInterface
     */
    protected $filterableAttributes;

    /**
     * @var Attribute[]
     */
    protected $filterAttributeMap;

    /**
     * NavigationContext constructor.
     *
     * @param RequestFactory $requestFactory
     * @param Client $client
     * @param Url $url
     * @param FilterableAttributeListInterface $filterableAttributes
     * @param TweakwiseFilterList $filterList
     */
    public function __construct(RequestFactory $requestFactory, Client $client, Url $url, FilterableAttributeListInterface $filterableAttributes, TweakwiseFilterList $filterList)
    {
        $this->requestFactory = $requestFactory;
        $this->client = $client;
        $this->url = $url;
        $this->filterableAttributes = $filterableAttributes;

        $filterList->setNavigationContext($this);
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
            $request = $this->getRequest();
            $this->initializeRequest($request);

            $this->response = $this->client->request($request);
        }

        return $this->response;
    }

    /**
     * @return Attribute[]
     */
    public function getFilterAttributeMap()
    {
        if ($this->filterAttributeMap === null) {
            $map = [];
            /** @var Attribute $attribute */
            foreach ($this->filterableAttributes->getList() as $attribute) {
                $map[$attribute->getData('attribute_code')] = $attribute;
            }
            $this->filterAttributeMap = $map;
        }
        return $this->filterAttributeMap;
    }

    /**
     * @param ProductNavigationRequest $request
     * @return $this
     */
    protected function initializeRequest(ProductNavigationRequest $request)
    {
        $this->url->apply($request);
        return $this;
    }
}