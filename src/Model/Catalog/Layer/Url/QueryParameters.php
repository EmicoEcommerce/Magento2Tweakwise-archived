<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Magento\Framework\UrlInterface as MagentoUrl;
use Zend\Http\Request as HttpRequest;

class QueryParameters implements UrlInterface
{
    /**
     * @var MagentoUrl
     */
    protected $url;

    /**
     * Magento constructor.
     *
     * @param MagentoUrl $url
     */
    public function __construct(MagentoUrl $url)
    {
        $this->url = $url;
    }

    /**
     * @param array $query
     * @return string
     */
    protected function getCurrentQueryUrl(array $query)
    {
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = true;
        return $this->url->getUrl('*/*/*', $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectFilter(HttpRequest $request, Item $item)
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        if ($settings->getSource() == SettingsType::SOURCE_CATEGORY) {
            return $this->getCategoryUrl($request, $item);
        } else {
            return $this->getQueryParamSelectUrl($request, $item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoveFilter(HttpRequest $request, Item $item)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getClearUrl(HttpRequest $request, Filter $facet)
    {

    }

    /**
     * Fetch current selected values
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string[]|string|null
     */
    protected function getRequestValues(HttpRequest $request, Item $item)
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        $urlKey = $settings->getUrlKey();

        $data = $request->getQuery($urlKey);
        if (!$data) {
            if ($settings->getIsMultipleSelect()) {
                return [];
            } else {
                return null;
            }
        }

        if ($settings->getIsMultipleSelect()) {
            if (!is_array($data)) {
                $data = [$data];
            }
            return array_map('strval', $data);
        } else {
            return (string) $data;
        }
    }

    /**
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    protected function getQueryParamSelectUrl(HttpRequest $request, Item $item)
    {
        $attribute = $item->getAttribute();
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        $urlKey = $settings->getUrlKey();
        $value = $attribute->getTitle();

        $values = $this->getRequestValues($request, $item);

        if ($settings->getIsMultipleSelect()) {
            $values[] = $value;
            $values = array_unique($values);

            $query = [$urlKey => $values];
        } else {
            $query = [$urlKey => $value];
        }

        return $this->getCurrentQueryUrl($query);
    }

    /**
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    protected function getCategoryUrl(HttpRequest $request, Item $item)
    {
        return '#';
    }
}