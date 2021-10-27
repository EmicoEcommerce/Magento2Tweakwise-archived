<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Client;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Emico\Tweakwise\Model\Client\RequestFactory;
use Emico\Tweakwise\Model\Client\Response\ProductNavigationResponse;
use Emico\Tweakwise\Model\Config;
use Emico\TweakwiseExport\Model\ProductAttributes;
use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Attribute;

/**
 * Class to keep navigation context for page request.
 * This ensures a single request for navigation data facet's and products.
 */
class NavigationContext
{
    /**
     * Visibility attribute code
     */
    public const VISIBILITY_ATTRIBUTE = 'visibility';

    /**
     * @var ProductNavigationRequest
     */
    protected $request = null;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ProductNavigationResponse|null
     */
    protected $response = null;

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
     * @var Config
     */
    protected $config;

    /**
     * @var ProductList
     */
    protected $productListHelper;

    /**
     * @var ToolbarModel
     */
    protected $toolbarModel;

    /**
     * @var Visibility
     */
    protected $visibility;

    /**
     * @var ProductAttributes
     */
    protected $productAttributes;

    /**
     * NavigationContext constructor.
     *
     * @param Config $config
     * @param RequestFactory $requestFactory
     * @param Client $client
     * @param Url $url
     * @param FilterableAttributeListInterface $filterableAttributes
     * @param CurrentContext $currentContext
     * @param ProductList $productListHelper
     * @param ToolbarModel $toolbarModel
     * @param ProductAttributes $productAttributes
     * @param Visibility $visibility
     */
    public function __construct(
        Config $config,
        RequestFactory $requestFactory,
        Client $client,
        Url $url,
        FilterableAttributeListInterface $filterableAttributes,
        CurrentContext $currentContext,
        ProductList $productListHelper,
        ToolbarModel $toolbarModel,
        ProductAttributes $productAttributes,
        Visibility $visibility
    ) {
        $this->config = $config;
        $this->requestFactory = $requestFactory;
        $this->client = $client;
        $this->url = $url;
        $this->filterableAttributes = $filterableAttributes;
        $this->productListHelper = $productListHelper;
        $this->toolbarModel = $toolbarModel;
        $this->visibility = $visibility;
        $this->productAttributes = $productAttributes;

        $currentContext->setContext($this);
    }

    /**
     * @return ProductNavigationRequest
     */
    public function getRequest(): ProductNavigationRequest
    {
        if (!$this->request) {
            $this->request = $this->requestFactory->create();
        }
        return $this->request;
    }

    /**
     * @return ProductNavigationResponse
     */
    public function getResponse(): ProductNavigationResponse
    {
        if (!$this->response) {
            $request = $this->getRequest();

            $this->initializeRequest($request);

            $this->response = $this->client->request($request);
        }

        return $this->response;
    }

    /**
     * Can be called if there was a response without triggering the creation of one.
     *
     * @return bool
     */
    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    /**
     * @return $this
     */
    public function resetPagination(): self
    {
        $params = $this->request->getParameters();
        $params['resetPagination'] = true;
        $params['tn_fk_p'] = 1;
        $params['tn_p'] = 1;

        $this->request = null;
        $request = $this->getRequest()->setParameters($params);
        $this->response = null;

        return $this->initializeRequest($request);
    }

    /**
     * @param $attributeCodes
     * @return Attribute[]
     */
    public function getFilterAttributeMap(array $attributeCodes = null): array
    {
        if ($this->filterAttributeMap === null) {
            $map = [];
            foreach ($this->productAttributes->getAttributesToExport($attributeCodes) as $attribute) {
                $map[$attribute->getData('attribute_code')] = $attribute;
            }
            $this->filterAttributeMap = $map;
        }

        return $this->filterAttributeMap;
    }

    /**
     * Retrieve current View mode simplified version
     * @see \Magento\Catalog\Block\Product\ProductList\Toolbar::getCurrentMode()
     *
     * @return string
     */
    public function getCurrentViewMode()
    {
        $availableModes = $this->productListHelper->getAvailableViewMode();
        $mode = $this->toolbarModel->getMode();

        /** @noinspection OffsetOperationsInspection */
        if ($mode && isset($availableModes[$mode])) {
            return $mode;
        }

        return $this->productListHelper->getDefaultViewMode($availableModes);
    }

    /**
     * @param ProductNavigationRequest $request
     * @return $this
     */
    protected function initializeRequest(ProductNavigationRequest $request)
    {
        // Apply magento config values
        $request->setLimit($this->productListHelper->getDefaultLimitPerPageValue($this->getCurrentViewMode()));
        $this->addVisibilityFilter($request);
        // Apply tweakwise config values
        if ($request instanceof ProductSearchRequest) {
            $templateId = $this->config->getSearchTemplateId();
            if ($templateId) {
                $request->setTemplateId($templateId);
            }
        }

        // Apply url values
        $this->url->apply($request);

        return $this;
    }

    /**
     * Add correct visibility filters based on type of Request
     *
     * @param ProductNavigationRequest $request
     */
    public function addVisibilityFilter(ProductNavigationRequest $request)
    {
        if ($request instanceof ProductSearchRequest) {
            $visibilityValues = $this->visibility->getVisibleInSearchIds();
        } else {
            $visibilityValues = $this->visibility->getVisibleInCatalogIds();
        }

        foreach ($visibilityValues as $visibilityValue) {
            $request->addHiddenParameter(self::VISIBILITY_ATTRIBUTE, $visibilityValue);
        }
    }
}
