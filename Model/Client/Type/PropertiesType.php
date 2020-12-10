<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Type;

/**
 * @method SortFieldType[] getSortFields();
 */
class PropertiesType extends Type
{
    /**
     * @param SortFieldType[]|array[] $sortFields
     * @return $this
     */
    public function setSortFields(array $sortFields)
    {
        $sortFields = $this->normalizeArray($sortFields, 'sortfield');

        $values = [];
        foreach ($sortFields as $value) {
            if (!$value instanceof SortFieldType) {
                $value = new SortFieldType($value);
            }

            $values[] = $value;
        }

        $this->data['sort_fields'] = $values;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return (int) $this->getDataValue('pagesize');
    }

    /**
     * @return int
     */
    public function getNumberOfItems()
    {
        return (int) $this->getDataValue('nrofitems');
    }

    /**
     * @return int
     */
    public function getNumberOfPages()
    {
        return (int) $this->getDataValue('nrofpages');
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return (int) $this->getDataValue('currentpage');
    }

    /**
     * @return int
     */
    public function getSelectedCategoryId()
    {
        return (int) $this->getDataValue('selectedcategory');
    }

    /**
     * @return int
     */
    public function getSearchTerm()
    {
        return (string) $this->getDataValue('searchterm');
    }

    /**
     * @return int
     */
    public function getSuggestedSearchTerm()
    {
        return (string) $this->getDataValue('suggestedsearchterm');
    }

    /**
     * @return bool
     */
    public function isDirectorySearch()
    {
        return $this->getBoolValue('isdirectsearch');
    }

    /**
     * @return bool
     */
    public function isRootCategory()
    {
        return $this->getBoolValue('isrootcategory');
    }

    /**
     * @return string
     */
    public function getPageUrl()
    {
        return (string) $this->getDataValue('pageurl');
    }

    /**
     * @return string
     */
    public function getResetUrl()
    {
        return (string) $this->getDataValue('reseturl');
    }
}