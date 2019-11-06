<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Block\Catalog\Product\ProductList;

use Emico\Tweakwise\Exception\ApiException;
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
    private $recommendationsContext;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var TemplateFinder
     */
    private $templateFinder;

    /**
     * Featured constructor.
     *
     * @param ProductContext $productContext
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param TemplateFinder $templateFinder
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
                try {
                    $this->configureRequest($this->recommendationsContext->getRequest());
                    $this->_productCollection = $this->recommendationsContext
                        ->getCollection();
                } catch (ApiException $e) {
                    $this->_productCollection = parent::_getProductCollection()
                        ->addFieldToFilter('entity_id', ['null' => true]);
                }
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