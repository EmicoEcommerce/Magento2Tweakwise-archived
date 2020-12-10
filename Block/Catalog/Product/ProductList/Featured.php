<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Block\Catalog\Product\ProductList;

use Emico\Tweakwise\Exception\ApiException;
use Emico\Tweakwise\MagentoCompat\PreparePostDataFactory;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Collection;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Context as RecommendationsContext;
use Emico\Tweakwise\Model\Client\Request\Recommendations\FeaturedRequest;
use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Config\TemplateFinder;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context as ProductContext;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;

/**
 * Class Featured
 *
 * @package Emico\Tweakwise\Block\Catalog\Product\ProductList
 *
 * @method string getRenderLocation();
 */
class Featured extends ListProduct
{
    /**
     * @var RecommendationsContext
     */
    protected $recommendationsContext;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var TemplateFinder
     */
    protected $templateFinder;

    /**
     * @var PreparePostDataFactory
     */
    protected $preparePostDataFactory;

    /**
     * Featured constructor.
     *
     * @param ProductContext $productContext
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param TemplateFinder $templateFinder
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param RecommendationsContext $recommendationsContext
     * @param Config $config
     * @param PreparePostDataFactory $preparePostDataFactory
     * @param array $data
     * @internal param CategoryRepositoryInterface $categoryRepository
     * @internal param Data $urlHelper
     * @internal param RecommendationsContext $recommendationsContext
     * @internal param Config $config
     * @internal param array $data
     */
    public function __construct(
        ProductContext $productContext,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        TemplateFinder $templateFinder,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        RecommendationsContext $recommendationsContext,
        Config $config,
        PreparePostDataFactory $preparePostDataFactory,
        array $data = []
    ) {
        parent::__construct(
            $productContext,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
        $this->recommendationsContext = $recommendationsContext;
        $this->config = $config;
        $this->templateFinder = $templateFinder;
        $this->preparePostDataFactory = $preparePostDataFactory;
    }

    /**
     * @return Collection
     */
    public function getProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        /*
         * Unfortunately class \Magento\Catalog\ViewModel\Product\Listing\PreparePostData
         * does not exist in magento 2.3 but is used in magento 2.4.
         * Magento_Catalog::product/list/items.phtml line 265 wants a PreparePostData from the block rendering the template
         * PreparePostDataFactory tries to get an instance of that class if it is available, if it is
         * we add it as a view model so that we remain compatible with magento 2.3 and lower.
         *
         * We dont add it if some view model is already registered
         */
        if (!$this->getData('view_model') && $this->preparePostDataFactory->getPreparePostData()) {
            $this->setData('view_model', $this->preparePostDataFactory->getPreparePostData());
        }
        try {
            $this->_getProductCollection();
        } catch (ApiException $e) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * {@inheritdoc}
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            if (!$this->checkRecommendationEnabled()) {
                $this->_productCollection = parent::_getProductCollection()
                    ->addFieldToFilter('entity_id', ['null' => true]);
            } else {
                $this->configureRequest($this->recommendationsContext->getRequest());
                $this->_productCollection = $this->recommendationsContext
                    ->getCollection();
            }
        }

        return $this->_productCollection;
    }

    /**
     * @return bool
     */
    protected function checkRecommendationEnabled(): bool
    {
        return $this->config->isRecommendationsEnabled(Config::RECOMMENDATION_TYPE_FEATURED);
    }

    /**
     * @param $request
     */
    protected function configureRequest(FeaturedRequest $request)
    {
        $category = $this->_coreRegistry->registry('current_category');
        if ($category instanceof Category) {
            $templateId = $this->templateFinder->forCategory($category, Config::RECOMMENDATION_TYPE_FEATURED);
        } else {
            $templateId = $this->config->getRecommendationsTemplate(Config::RECOMMENDATION_TYPE_FEATURED);
        }

        $request->setTemplate($templateId);
    }
}
