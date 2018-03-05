<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Request;

use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Response\ProductNavigationResponse;
use Magento\Catalog\Model\Category;
use Emico\TweakwiseExport\Model\Helper;
use Magento\Store\Model\StoreManager;

class ProductNavigationRequest extends Request
{
    /**
     * Maximum number of products returned for one request
     */
    const MAX_PRODUCTS = 1000;

    /**
     * Sort order directions
     */
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    /**
     * {@inheritDoc}
     */
    protected $path = 'navigation';

    /**
     * Request constructor.
     *
     * @param Helper $helper
     * @param StoreManager $storeManager
     */
    public function __construct(Helper $helper, StoreManager $storeManager)
    {
        parent::__construct($helper, $storeManager);
        $this->addAttributeFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $this->addAttributeFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG);
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseType()
    {
        return ProductNavigationResponse::class;
    }

    /**
     * @param Category|int $category
     * @return $this
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Category) {
            $categoryId = $category->getId();
            $storeId = $category->getStoreId();
        } else {
            $categoryId = (int) $category;
            $storeId = $this->storeManager->getStore()->getId();
        }

        $tweakwiseId = $this->helper->getTweakwiseId($storeId, $categoryId);
        $this->addParameter('tn_cid', $tweakwiseId, '-');
        return $this;
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return $this
     */
    public function addAttributeFilter($attribute, $value)
    {
        $this->addParameter('tn_fk_' . $attribute, $value);
        return $this;
    }

    /**
     * @param string $sort
     * @return $this
     */
    public function setOrder($sort)
    {
        $this->setParameter('tn_sort', $sort);
        return $this;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function setPage($page)
    {
        $page = (int) $page;
        $page = max(1, $page);

        $this->setParameter('tn_p', $page);
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        if ($limit === 'all') {
            $limit = self::MAX_PRODUCTS;
        }
        $limit = min($limit, self::MAX_PRODUCTS);
        $this->setParameter('tn_ps', $limit);
        return $this;
    }

    /**
     * @param int|null $templateId
     * @return $this
     */
    public function setTemplateId($templateId)
    {
        $this->setParameter('tn_ft', $templateId);
        return $this;
    }
}