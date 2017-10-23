<?php
/**
 * @author Emico <info@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

namespace Emico\Tweakwise\Block\Catalog\Product\ProductList;

use Emico\Tweakwise\Exception\InvalidArgumentException;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Collection;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Context;
use Emico\Tweakwise\Model\Client\Request\Recommendations\ProductRequest;
use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Config\TemplateFinder;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;

abstract class AbstractRecommendationPlugin
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var TemplateFinder
     */
    protected $templateFinder;

    /**
     * Plugin constructor.
     *
     * @param Config $config
     * @param Registry $registry
     * @param Context $context
     * @param TemplateFinder $templateFinder
     */
    public function __construct(Config $config, Registry $registry, Context $context, TemplateFinder $templateFinder)
    {
        $this->config = $config;
        $this->registry = $registry;
        $this->context = $context;
        $this->templateFinder = $templateFinder;
    }

    /**
     * @return string
     */
    protected abstract function getType();

    /**
     * @param ProductRequest $request
     */
    protected function configureRequest(ProductRequest $request)
    {
        $product = $this->registry->registry('product');
        if (!$product instanceof Product) {
            return;
        }

        $request->setProduct($product);
        $request->setTemplate($this->templateFinder->forProduct($product, $this->getType()));
    }

    /**
     * @return Collection
     */
    protected function getCollection()
    {
        if (!$this->collection) {
            $request = $this->context->getRequest();
            if (!$request instanceof ProductRequest) {
                throw new InvalidArgumentException('Set context should contain ProductRequest');
            }

            $this->configureRequest($request);
            $this->collection = $this->context->getCollection();
        }

        return $this->collection;
    }
}