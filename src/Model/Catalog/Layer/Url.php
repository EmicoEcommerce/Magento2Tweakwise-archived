<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
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
    protected $implementation;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * Builder constructor.
     *
     * @param UrlInterface $implementation
     * @param HttpRequest $request
     */
    public function __construct(UrlInterface $implementation, HttpRequest $request)
    {
        $this->implementation = $implementation;
        $this->request = $request;
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getSelectFilter(Item $item)
    {
        return $this->implementation->getSelectFilter($this->request, $item);
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getRemoveFilter(Item $item)
    {
        return $this->implementation->getRemoveFilter($this->request, $item);
    }

    /**
     * @param Item[] $activeFilterItems
     * @return string
     */
    public function getClearUrl(array $activeFilterItems)
    {
        return $this->implementation->getClearUrl($this->request, $activeFilterItems);
    }

    /**
     * @param ProductNavigationRequest $navigationRequest
     */
    public function apply(ProductNavigationRequest $navigationRequest)
    {
        $this->implementation->apply($this->request, $navigationRequest);
    }

    /**
     * @param Filter $facet
     * @return string
     */
    public function getSliderUrl(Filter $facet)
    {
        return $this->implementation->getSlider($this->request, $facet);
    }
}