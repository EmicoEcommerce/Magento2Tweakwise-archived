<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\TargetRule\Catalog\Product\ProductList;

use Closure;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Collection;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\CollectionFactory;
use Emico\Tweakwise\Model\Client;
use Emico\Tweakwise\Model\Client\Request\Recommendations\ProductRequest;
use Emico\Tweakwise\Model\Client\RequestFactory;
use Emico\Tweakwise\Model\Client\Response\RecommendationsResponse;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Registry;
use Magento\TargetRule\Block\Catalog\Product\ProductList\AbstractProductList;

class Plugin
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var RecommendationsResponse
     */
    private $response;

    /**
     * @var ProductRequest
     */
    private $request;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var CatalogConfig
     */
    private $catalogConfig;

    /**
     * @var Visibility
     */
    private $visibility;

    /**
     * Plugin constructor.
     *
     * @param string $type
     * @param Config $config
     * @param Client $client
     * @param RequestFactory $requestFactory
     * @param Registry $registry
     * @param CollectionFactory $collectionFactory
     * @param CatalogConfig $catalogConfig
     * @param Visibility $visibility
     */
    public function __construct($type, Config $config, Client $client, RequestFactory $requestFactory, Registry $registry,
        CollectionFactory $collectionFactory, CatalogConfig $catalogConfig, Visibility $visibility)
    {
        $this->type = (string) $type;
        $this->config = $config;
        $this->client = $client;
        $this->registry = $registry;
        $this->requestFactory = $requestFactory;
        $this->collectionFactory = $collectionFactory;
        $this->catalogConfig = $catalogConfig;
        $this->visibility = $visibility;
    }

    /**
     * @param AbstractProductList $subject
     * @param Closure $proceed
     * @return Collection
     */
    public function aroundGetItemCollection(AbstractProductList $subject, Closure $proceed)
    {
        if (!$this->config->isRecommendationsEnabled($this->type)) {
            return $proceed();
        }

        return $this->getCollection();
    }

    /**
     * @param AbstractProductList $subject
     * @param Closure $proceed
     * @return int
     */
    public function aroundGetPositionLimit(AbstractProductList $subject, Closure $proceed)
    {
        if (!$this->config->isRecommendationsEnabled($this->type)) {
            return $proceed();
        }

        return $this->getCollection()->count();
    }

    /**
     * @param ProductRequest $request
     */
    private function configureRequest(ProductRequest $request)
    {
        $product = $this->registry->registry('product');
        if ($product instanceof Product) {
            $request->setProduct($product);
        }

        $request->setTemplate($this->config->getRecommendationsTemplate($this->type));
    }

    /**
     * @return ProductRequest
     */
    private function getRequest()
    {
        if (!$this->request) {
            $this->request = $this->requestFactory->create();
            $this->configureRequest($this->request);
        }

        return $this->request;
    }

    /**
     * @return RecommendationsResponse
     */
    private function getResponse()
    {
        if (!$this->response) {
            $this->response = $this->client->request($this->getRequest());
        }
        return $this->response;
    }

    /**
     * @return Collection
     */
    private function getCollection()
    {
        if (!$this->collection) {
            $collection = $this->collectionFactory->create(['response' => $this->getResponse()]);
            $this->prepareCollection($collection);
            $this->collection = $collection;
        }

        return $this->collection;
    }

    /**
     * @param Collection $collection
     */
    private function prepareCollection(Collection $collection)
    {
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addUrlRewrite()
            ->setVisibility($this->visibility->getVisibleInCatalogIds())
            ->setFlag('do_not_use_category_id', true);
    }
}