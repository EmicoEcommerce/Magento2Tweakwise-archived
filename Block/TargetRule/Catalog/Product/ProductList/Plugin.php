<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\TargetRule\Catalog\Product\ProductList;

use Closure;
use Emico\Tweakwise\Exception\ApiException;
use Emico\Tweakwise\Exception\InvalidArgumentException;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Collection;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Context;
use Emico\Tweakwise\Model\Client\Request\Recommendations\ProductRequest;
use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Config\TemplateFinder;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\TargetRule\Block\Catalog\Product\ProductList\AbstractProductList;

abstract class Plugin
{
    /**
     * @var string
     */
    protected string $type;

    /**
     * @var Collection
     */
    protected Collection $collection;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var Registry
     */
    protected Registry $registry;

    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var TemplateFinder
     */
    protected TemplateFinder $templateFinder;

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
     * @param AbstractProductList $subject
     * @param Closure $proceed
     * @return array
     */
    public function aroundGetItemCollection(AbstractProductList $subject, Closure $proceed): array
    {
        if (!$this->config->isRecommendationsEnabled($this->type)) {
            return $proceed();
        }

        try {
            return $this->getCollection()->getItems();
        } catch (ApiException $e) {
            return $proceed();
        }
    }

    /**
     * @param AbstractProductList $subject
     * @param Closure $proceed
     * @return int
     */
    public function aroundGetPositionLimit(AbstractProductList $subject, Closure $proceed): int
    {
        if (!$this->config->isRecommendationsEnabled($this->type)) {
            return $proceed();
        }

        try {
            return $this->getCollection()->count();
        } catch (ApiException $e) {
            return $proceed();
        }
    }

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
        $request->setTemplate($this->templateFinder->forProduct($product, $this->type));
    }

    /**
     * @return Collection
     */
    protected function getCollection(): Collection
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
