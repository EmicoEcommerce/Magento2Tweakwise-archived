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
    protected string $path = 'navigation';

    /**
     * @var array
     */
    protected array $hiddenParameters = [];

    /**
     * {@inheritdoc}
     */
    public function getResponseType(): string
    {
        return ProductNavigationResponse::class;
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return $this
     */
    public function addAttributeFilter(string $attribute, string $value): self
    {
        $this->addParameter('tn_fk_' . $attribute, $value);
        return $this;
    }

    /**
     * @param string $attribute
     * @param $value
     */
    public function addHiddenParameter(string $attribute, $value)
    {
        $this->hiddenParameters[] = sprintf('%s=%s', $attribute, $value);
        $this->setParameter('tn_parameters', implode('&', $this->hiddenParameters));
    }

    /**
     * @param string $sort
     * @return $this
     */
    public function setOrder(string $sort): self
    {
        $this->setParameter('tn_sort', $sort);
        return $this;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function setPage(int $page): self
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
    public function setLimit(int $limit): self
    {
        $limit = min($limit, self::MAX_PRODUCTS);
        $this->setParameter('tn_ps', $limit);
        return $this;
    }

    /**
     * @param int|null $templateId
     * @return $this
     */
    public function setTemplateId(?int $templateId): self
    {
        $this->setParameter('tn_ft', $templateId);
        return $this;
    }

    /**
     * @param int|null $templateId
     * @return $this
     */
    public function setSortTemplateId(?int $templateId): self
    {
        $this->setParameter('tn_st', $templateId);
        return $this;
    }

    /**
     * @param string $profileKey
     * @return $this
     */
    public function setProfileKey(string $profileKey): self
    {
        $this->setParameter('tn_profilekey', $profileKey);
        return $this;
    }
}
