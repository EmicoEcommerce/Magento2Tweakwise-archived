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
    public function setSortFields(array $sortFields): self
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
    public function getPageSize(): int
    {
        return (int) $this->getDataValue('pagesize');
    }

    /**
     * @return int
     */
    public function getNumberOfItems(): int
    {
        return (int) $this->getDataValue('nrofitems');
    }

    /**
     * @return int
     */
    public function getNumberOfPages(): int
    {
        return (int) $this->getDataValue('nrofpages');
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return (int) $this->getDataValue('currentpage');
    }

    /**
     * @return int
     */
    public function getSelectedCategoryId(): int
    {
        return (int) $this->getDataValue('selectedcategory');
    }

    /**
     * @return string
     */
    public function getSearchTerm(): string
    {
        return (string) $this->getDataValue('searchterm');
    }

    /**
     * @return string
     */
    public function getSuggestedSearchTerm(): string
    {
        return (string) $this->getDataValue('suggestedsearchterm');
    }

    /**
     * @return bool
     */
    public function isDirectorySearch(): bool
    {
        return $this->getBoolValue('isdirectsearch');
    }

    /**
     * @return bool
     */
    public function isRootCategory(): bool
    {
        return $this->getBoolValue('isrootcategory');
    }

    /**
     * @return string
     */
    public function getPageUrl(): string
    {
        return (string) $this->getDataValue('pageurl');
    }

    /**
     * @return string
     */
    public function getResetUrl(): string
    {
        return (string) $this->getDataValue('reseturl');
    }
}
