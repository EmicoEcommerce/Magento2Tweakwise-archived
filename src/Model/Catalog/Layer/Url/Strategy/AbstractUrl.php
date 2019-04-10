<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\Url\CategoryUrlInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlModel;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\Tweakwise\Model\Config;
use Emico\TweakwiseExport\Model\Helper as ExportHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Zend\Http\Request as HttpRequest;

abstract class AbstractUrl implements UrlInterface, FilterApplierInterface, CategoryUrlInterface
{
    /**
     * Commonly used query parameters from headers
     */
    const PARAM_LIMIT = 'product_list_limit';
    const PARAM_ORDER = 'product_list_order';
    const PARAM_PAGE = 'p';
    const PARAM_SEARCH = 'q';

    /**
     * @var UrlModel
     */
    protected $url;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ExportHelper
     */
    protected $exportHelper;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Magento constructor.
     *
     * @param UrlModel $url
     * @param CategoryRepository $categoryRepository
     * @param ExportHelper $exportHelper
     * @param Config $config
     */
    public function __construct(UrlModel $url, CategoryRepository $categoryRepository, ExportHelper $exportHelper, Config $config)
    {
        $url->setConfig($config);
        $this->url = $url;
        $this->categoryRepository = $categoryRepository;
        $this->exportHelper = $exportHelper;
        $this->config = $config;
    }

    /**
     * Get url when attribute option is selected
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    protected abstract function getAttributeSelectUrl(HttpRequest $request, Item $item);

    /**
     * Get url when attribute option is removed
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    protected abstract function getAttributeRemoveUrl(HttpRequest $request, Item $item);

    /**
     * Fetch a list of category ID's to filter
     * @param HttpRequest $request
     * @return int[]
     */
    protected abstract function getCategoryFilters(HttpRequest $request);

    /**
     * Fetches all filters that should be applied on Tweakwise Request. In the format
     *
     * [
     *     // Single select attributes
     *     'attribute' => 'value',
     *     // Multi select attributes
     *     'attribute' => ['value1', 'value2'],
     * ]
     * @param HttpRequest $request
     * @return array
     */
    protected abstract function getAttributeFilters(HttpRequest $request);
}