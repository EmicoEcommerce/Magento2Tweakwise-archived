<?php
/**
 * @author Emico <info@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

namespace Emico\Tweakwise\Block\Catalog\Product\ProductList;

use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Collection;
use Emico\Tweakwise\Model\Catalog\Product\Recommendation\Context as RecommendationsContext;
use Emico\Tweakwise\Model\Client\Request\Recommendations\FeaturedRequest;
use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Config\Source\FeaturedLocation;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context as ProductContext;
use Magento\Catalog\Block\Product\ListProduct;
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
     * Featured constructor.
     *
     * @param ProductContext $productContext
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param RecommendationsContext $recommendationsContext
     * @param Config $config
     * @param array $data
     */
    public function __construct(ProductContext $productContext, PostHelper $postDataHelper, Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository, Data $urlHelper, RecommendationsContext $recommendationsContext, Config $config, array $data = [])
    {
        parent::__construct($productContext, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
        $this->recommendationsContext = $recommendationsContext;
        $this->config = $config;
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
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->configureRequest($this->recommendationsContext->getRequest());
            $this->_productCollection = $this->recommendationsContext->getCollection();
        }

        return $this->_productCollection;
    }

    /**
     * @param $request
     */
    private function configureRequest(FeaturedRequest $request)
    {
        $templateId = $this->config->getRecommendationsTemplate(Config::RECOMMENDATION_TYPE_FEATURED);
        $request->setTemplate($templateId);
    }
}