<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\CatalogPermissions\Model\Plugin\Catalog\Model\Layer\FilterList;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\CatalogPermissions\Model\Plugin\Catalog\Model\Layer\FilterList as FilterListPlugin;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Catalog\Model\Entity\AttributeFactory;
use Magento\Framework\Exception\LocalizedException;

class Plugin
{
    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * Plugin constructor.
     * @param AttributeFactory $attributeFactory
     */
    public function __construct(AttributeFactory $attributeFactory)
    {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @param FilterListPlugin $subject
     * @param FilterList $filterListModel
     * @param array $filterList
     * @return mixed
     * @throws LocalizedException
     */
    public function beforeAfterGetFilters(FilterListPlugin $subject, FilterList $filterListModel, array $filterList)
    {
        /** @var Filter $filter */
        foreach ($filterList as $filter) {
            if ($filter->getAttributeModel()) {
                continue;
            }
            $this->initAttributeModel($filter);
        }
    }

    /**
     * @param AbstractFilter $filter
     */
    private function initAttributeModel(Filter $filter): void
    {
        $attributeModel = $this->attributeFactory->create([]);
        $attributeModel->setAttributeCode($filter->getUrlKey());
        $filter->setAttributeModel($attributeModel);
    }
}