<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\UrlStrategyFactory;
use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Zend\Http\Request as HttpRequest;

/**
 * Class Url will later implement logic to use implementation selected in configuration.
 *
 * @package Emico\Tweakwise\Model\Catalog\Layer
 */
class Url
{
    /**
     * @var UrlInterface
     */
    protected $urlStrategy;

    /**
     * @var FilterApplierInterface
     */
    protected $filterApplier;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * Builder constructor.
     *
     * @param UrlStrategyFactory $urlStrategyFactory
     * @param FilterApplierInterface $defaultFilterApplier
     * @param HttpRequest $request
     */
    public function __construct(UrlStrategyFactory $urlStrategyFactory, FilterApplierInterface $defaultFilterApplier, HttpRequest $request)
    {
        //@todo can we create a custom factory?
        $this->urlStrategy = $urlStrategyFactory->create();
        if ($this->urlStrategy instanceof FilterApplierInterface) {
            $this->filterApplier = $this->urlStrategy;
        } else {
            $this->filterApplier = $defaultFilterApplier;
        }
        $this->request = $request;
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getSelectFilter(Item $item)
    {
        return $this->urlStrategy->getSelectFilter($this->request, $item);
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getRemoveFilter(Item $item)
    {
        return $this->urlStrategy->getRemoveFilter($this->request, $item);
    }

    /**
     * @param Item[] $activeFilterItems
     * @return string
     */
    public function getClearUrl(array $activeFilterItems)
    {
        return $this->urlStrategy->getClearUrl($this->request, $activeFilterItems);
    }

    /**
     * @param ProductNavigationRequest $navigationRequest
     */
    public function apply(ProductNavigationRequest $navigationRequest)
    {
        $this->filterApplier->apply($this->request, $navigationRequest);
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getSliderUrl(Item $item)
    {
        return $this->urlStrategy->getSlider($this->request, $item);
    }
}