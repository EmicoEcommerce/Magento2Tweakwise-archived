<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Magento\Catalog\Model\Category;
use Zend\Http\Request as HttpRequest;

class QueryParameters extends AbstractUrl
{
    /**
     * Separator used in category tree urls
     */
    const CATEGORY_TREE_SEPARATOR = '-';

    /**
     * Extra ignored page parameters
     */
    const PARAM_MODE = 'product_list_mode';
    const PARAM_CATEGORY = 'categorie';

    /**
     * Parameters to be ignored as attribute filters
     *
     * @var string[]
     */
    protected $ignoredQueryParameters = [
        self::PARAM_CATEGORY,
        self::PARAM_ORDER,
        self::PARAM_LIMIT,
        self::PARAM_MODE,
        self::PARAM_SEARCH,
    ];

    /**
     * {@inheritdoc}
     */
    public function getClearUrl(HttpRequest $request, array $activeFilterItems)
    {
        $query = [];
        /** @var Item $item */
        foreach ($activeFilterItems as $item) {
            $filter = $item->getFilter();
            $facet = $filter->getFacet();
            $settings = $facet->getFacetSettings();

            $urlKey = $settings->getUrlKey();

            $query[$urlKey] = $filter->getCleanValue();
        }

        return $this->getCurrentQueryUrl($query);
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
        $params['_escape'] = false;
        return $this->url->getUrl('*/*/*', $params);
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
     * {@inheritdoc}
     */
    protected function getCategoryTreeSelectUrl(HttpRequest $request, Item $item, Category $category)
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();
        $urlKey = $settings->getUrlKey();

        $requestData = $request->getQuery($urlKey);
        if (!$requestData) {
            $requestData = [];
        } else {
            $requestData = explode(self::CATEGORY_TREE_SEPARATOR, $requestData);
        }

        $categoryId = $category->getId();
        if (!in_array($categoryId, $requestData)) {
            $requestData[] = $categoryId;
        }

        $query = [$urlKey => join(self::CATEGORY_TREE_SEPARATOR, $requestData)];
        return $this->getCurrentQueryUrl($query);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCategoryTreeRemoveUrl(HttpRequest $request, Item $item, Category $category)
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();
        $urlKey = $settings->getUrlKey();

        $requestData = $request->getQuery($urlKey);
        if (!$requestData) {
            $requestData = [];
        } else {
            $requestData = explode(self::CATEGORY_TREE_SEPARATOR, $requestData);
        }

        $categoryId = $category->getId();
        $index = array_search($categoryId, $requestData);
        if ($index !== false) {
            array_splice($requestData, $index);
        }

        if (count($requestData)) {
            $value = join(self::CATEGORY_TREE_SEPARATOR, $requestData);
        } else {
            $value = $filter->getResetValue();
        }

        $query = [$urlKey => $value];
        return $this->getCurrentQueryUrl($query);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCategoryFilterSelectUrl(HttpRequest $request, Item $item, Category $category)
    {
        $attribute = $item->getAttribute();
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        $urlKey = $settings->getUrlKey();
        $value = $attribute->getTitle();

        $query = [$urlKey => $value];
        return $this->getCurrentQueryUrl($query);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCategoryFilterRemoveUrl(HttpRequest $request, Item $item, Category $category)
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        $urlKey = $settings->getUrlKey();

        $query = [$urlKey => $filter->getCleanValue()];
        return $this->getCurrentQueryUrl($query);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeSelectUrl(HttpRequest $request, Item $item)
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
     * {@inheritdoc}
     */
    protected function getAttributeRemoveUrl(HttpRequest $request, Item $item)
    {
        $filter = $item->getFilter();
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        $urlKey = $settings->getUrlKey();

        if ($settings->getIsMultipleSelect()) {
            $attribute = $item->getAttribute();
            $value = $attribute->getTitle();
            $values = $this->getRequestValues($request, $item);

            $index = array_search($value, $values);
            if ($index !== false) {
                unset($values[$index]);
            }

            $query = [$urlKey => $values];
        } else {
            $query = [$urlKey => $filter->getCleanValue()];
        }

        return $this->getCurrentQueryUrl($query);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCategoryFilters(HttpRequest $request)
    {
        $categories = $request->getQuery('categorie');
        $categories = explode(self::CATEGORY_TREE_SEPARATOR, $categories);
        $categories = array_map('intval', $categories);
        $categories = array_filter($categories);
        $categories = array_unique($categories);

        return $categories;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeFilters(HttpRequest $request)
    {
        $result = [];
        foreach ($request->getQuery() as $attribute => $value) {
            if (in_array(mb_strtolower($attribute), $this->ignoredQueryParameters)) {
                continue;
            }

            $result[$attribute] = $value;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlider(HttpRequest $request, Filter $filter)
    {
        $facet = $filter->getFacet();
        $settings = $facet->getFacetSettings();

        $urlKey = $settings->getUrlKey();
        $query = [$urlKey => '{{from}}-{{to}}'];

        return $this->getCurrentQueryUrl($query);
    }
}